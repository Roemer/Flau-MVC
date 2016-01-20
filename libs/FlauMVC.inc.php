<?php

/**
 * FlauMVC.inc.php
 * Provides a simple MVC framework for php
 * 
 * @author Roman Baeriswyl
 * @version 1.0
 * @copyright Copyright &copy; 2016 Roman Baeriswyl
 */

namespace FlauMVC;

/**
 * Static class which holds the basic settings
 */
abstract class Settings {

    /**
     * Root-Path where the MVC folders and files are
     * Defaults to $_SERVER['DOCUMENT_ROOT']/mvc
     */
    public static $MvcRootPath;

    /**
     * Name of the front controller file
     * Defaults to index.php
     */
    public static $FrontControllerName;

}

// Initialize the settings with default values
Settings::$MvcRootPath = $_SERVER['DOCUMENT_ROOT'] . '/mvc';
Settings::$FrontControllerName = 'index.php';

/**
 * Enum for the various asset types which can be registered
 */
abstract class AssetType {

    const JavaScript = 1;
    const StyleSheet = 2;

}

/**
 * Enum for the source of the asset
 */
abstract class AssetSource {

    const Text = 1;
    const Link = 2;

}

/**
 * Enum for the location, where the asset should be loaded
 */
abstract class AssetLocation {

    const Bottom = 1;
    const Head = 2;

}

/**
 * Base class for all view models
 */
abstract class BaseViewModel {

    /**
     * Title of the page
     */
    public $PageTitle;

    /**
     * Private members
     */
    private $_currentRegisterAssetType;
    private $_currentRegisterAssetSource;
    private $_currentRegisterAssetLocation;
    private $_assets;

    /**
     * Constructor
     */
    public function __construct() {
        // Initialize the asset array
        $this->_assets = array();
        $this->_assets[AssetType::JavaScript] = array();
        $this->_assets[AssetType::JavaScript][AssetLocation::Bottom] = array();
        $this->_assets[AssetType::JavaScript][AssetLocation::Head] = array();
        $this->_assets[AssetType::StyleSheet] = array();
        $this->_assets[AssetType::StyleSheet][AssetLocation::Bottom] = array();
        $this->_assets[AssetType::StyleSheet][AssetLocation::Head] = array();
    }

    /**
     * Method to register an asset
     */
    public function registerAsset($asset, $type = AssetType::JavaScript, $source = AssetSource::Text, $location = AssetLocation::Bottom) {
        $assetContent = $asset;
        if ($source == AssetSource::Link) {
            $assetContent = $this->wrapAssetInInclude($asset, $type);
        }

        $this->_assets[$type][$location][] = $assetContent;
    }

    public function registerJavaScript($asset, $source = AssetSource::Text, $location = AssetLocation::Bottom) {
        $this->registerAsset($asset, AssetType::JavaScript, $source, $location);
    }

    public function registerStyleSheet($asset, $source = AssetSource::Text, $location = AssetLocation::Bottom) {
        $this->registerAsset($asset, AssetType::StyleSheet, $source, $location);
    }

    public function startRegisterAsset($type = AssetType::JavaScript, $source = AssetSource::Text, $location = AssetLocation::Bottom) {
        $this->_currentRegisterAssetType = $type;
        $this->_currentRegisterAssetSource = $source;
        $this->_currentRegisterAssetLocation = $location;
        ob_start();
    }

    public function endRegisterAsset() {
        $output = ob_get_clean();
        $this->registerAsset($output, $this->_currentRegisterAssetType, $this->_currentRegisterAssetSource, $this->_currentRegisterAssetLocation);
    }

    public function startRegisterJavaScript($source = AssetSource::Text, $location = AssetLocation::Bottom) {
        $this->startRegisterAsset(AssetType::JavaScript, $source, $location);
    }

    public function endRegisterJavaScript() {
        $this->endRegisterAsset();
    }

    public function startRegisterStyleSheet($source = AssetSource::Text, $location = AssetLocation::Bottom) {
        $this->startRegisterAsset(AssetType::StyleSheet, $source, $location);
    }

    public function endRegisterStyleSheet() {
        $this->endRegisterAsset();
    }

    /**
     * Get the assets for the given parameters
     */
    public function getAssets($type = AssetType::JavaScript, $location = AssetLocation::Bottom) {
        return $this->_assets[$type][$location];
    }

    /**
     * Wraps an asset link in the correct html code
     */
    private function wrapAssetInInclude($asset, $type) {
        if ($type == AssetType::JavaScript) {
            return '<script src="' . $asset . '" type="text/javascript"></script>';
        } else if ($type == AssetType::StyleSheet) {
            return '<link rel="stylesheet" type="text/css" href="' . $asset . '">';
        }
        return $asset;
    }

}

/**
 * Base class for all controllers
 */
abstract class BaseController {

    protected $viewFile;

    public function __construct() {
        // Load all the models for this controller
        $this->loadModelFiles();
    }

    // Loads all the models for this controller
    private function loadModelFiles() {
        // Include all the models for this controller
        $controllerName = $this->getControllerName();
        $this->requireAllFilesFromFolder(Settings::$MvcRootPath . '/models/' . $controllerName);
    }

    // Execute the given action
    public function executeAction($action, $model = NULL) {
        if (is_null($model)) {
            return $this->{$action}();
        } else {
            return $this->{$action}($model);
        }
    }

    public function outputView($viewName, $viewModel = NULL, $template = 'Master') {
        // Get the controller's name
        $controllerName = $this->getControllerName();
        // Build the path to the viewfile
        $this->viewFile = Settings::$MvcRootPath . '/views/' . $controllerName . '/' . $viewName . '.php';

        // Check if the requested view file exists
        if (file_exists($this->viewFile)) {
            // Check if a template was given
            if ($template) {
                // Build the path to the template file
                $templateFile = Settings::$MvcRootPath . '/views/' . $template . '.php';
                // Try to include the template file
                if (file_exists($templateFile)) {
                    require($templateFile);
                } else {
                    // Error with template file
                    require(Settings::$MvcRootPath . '/views/Error/BadTemplate.php');
                }
            } else {
                // No template, just show the view directly
                require($this->viewFile);
            }
        } else {
            // Error with the view
            require(Settings::$MvcRootPath . '/views/Error/BadView.php');
        }
    }

    /**
     * Gets the name of the current controller
     */
    private function getControllerName() {
        // Parse out the controller's name
        $controllerName = substr(get_class($this), 0, strlen('Controller') * -1);
        return $controllerName;
    }

    /**
     * Loads all files (once) from the given folder
     */
    private function requireAllFilesFromFolder($folder) {
        foreach (glob("{$folder}/*.php") as $filename) {
            require_once $filename;
        }
    }

}

/**
 * Loader class which handles the redirecton to the right controller
 */
class Loader {

    public $ControllerName;
    public $ControllerClass;
    public $Action;
    public $UrlValues;

    public static function getCurrentController() {
        // Read out the controller
        if ($_GET['controller'] == "") {
            // Default controller
            $controllerName = "Home";
        } else {
            $controllerName = $_GET['controller'];
        }
        return $controllerName;
    }

    public static function getCurrentAction() {
        if ($_GET['action'] == "") {
            // Default action
            $action = 'Show';
        } else {
            $action = $_GET['action'];
        }
        return $action;
    }

    // Read out the parameters from the url
    public function loadFromUrl() {
        $this->UrlValues = $_GET;

        // Read out the controller
        $controllerName = self::getCurrentController();

        // Read out the action
        $action = self::getCurrentAction();

        $this->loadDirectly($controllerName, $action);
    }

    // Directly load a controller by name
    public function loadDirectly($controllerName, $action = NULL) {
        $this->UrlValues = $_GET;
        $this->ControllerName = ucfirst(strtolower($controllerName));
        $this->ControllerClass = $this->ControllerName . "Controller";
        $this->Action = ucfirst($action);
    }

    // Create the appropriate controller
    public function createController() {
        // Check if the requested controller class file exists and require it if so
        if (file_exists(Settings::$MvcRootPath . '/controllers/' . $this->ControllerClass . '.php')) {
            require(Settings::$MvcRootPath . '/controllers/' . $this->ControllerClass . '.php');
        } else {
            return $this->createErrorController();
        }

        // Check if the controller-class exists
        if (class_exists($this->ControllerClass)) {
            $parents = class_parents($this->ControllerClass);
            // Check that it extends the BaseController
            if (in_array("FlauMVC\BaseController", $parents)) {
                // Check if it contains the action
                if (method_exists($this->ControllerClass, $this->Action)) {
                    // Create the controller
                    return new $this->ControllerClass();
                } else {
                    // Bad action/method error
                    return $this->createErrorController();
                }
            } else {
                // Bad controller error
                return $this->createErrorController();
            }
        } else {
            // Bad controller error
            return $this->createErrorController();
        }
    }

    private function createErrorController() {
        require(Settings::$MvcRootPath . '/controllers/ErrorController.php');
        $this->Action = 'BadUrl';
        return new \ErrorController();
    }

}

/**
 * Various helpful utilities
 */
class Utilities {

    /**
     * Redirects to the given controller / action
     * @param type $immediatelyExit Flag to set if the execution should immediately stop
     * @param type $controller The controller to redirect to
     * @param type $action The action to redirect to
     * @param array $additionalData Additional url parameters
     */
    static function redirectToAction($immediatelyExit, $controller, $action, array $additionalData = NULL) {
        $url = self::buildUrl($controller, $action, $additionalData);
        header('Location: ' . $url);
        if ($immediatelyExit) {
            exit();
        }
    }

    /**
     * Builds an url to the given controller / action
     * @param type $controller The controller the url should point to
     * @param type $action The action the url should point to
     * @param array $additionalData dditional url parameters
     * @return string The complete url
     */
    static function buildUrl($controller, $action, array $additionalData = NULL) {
        $url = Settings::$FrontControllerName . "?controller=$controller&action=$action";
        if (!is_null($additionalData) && !empty($additionalData)) {
            $additionalQuery = http_build_query($additionalData);
            if (strlen($additionalQuery) > 0) {
                $url .= '&' . $additionalQuery;
            }
        }
        return $url;
    }

}
