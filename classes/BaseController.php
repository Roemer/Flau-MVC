<?php

abstract class BaseController {

    protected $viewFile;

    public function __construct() {
        // Load all the models for this controller
        $this->LoadModelFiles();
    }

    // Loads all the models for this controller
    private function LoadModelFiles() {
        // Include all the models for this controller
        $controllerName = $this->GetControllerName();
        Utilities::RequireAllFilesFromFolder(Utilities::GetMvcRoot() . '/models/' . $controllerName);
    }

    // Execute the given action
    public function ExecuteAction($action, $model = NULL) {
        if (is_null($model)) {
            return $this->{$action}();
        } else {
            return $this->{$action}($model);
        }
    }

    public function OutputView($viewName, $viewModel = NULL, $template = 'Master') {
        // Get the controller's name
        $controllerName = $this->GetControllerName();
        // Build the path to the viewfile
        $this->viewFile = Utilities::GetMvcRoot() . '/views/' . $controllerName . '/' . $viewName . '.php';

        // Check if the requested view file exists
        if (file_exists($this->viewFile)) {
            // Check if a template was given
            if ($template) {
                // Build the path to the template file
                $templateFile = Utilities::GetMvcRoot() . '/views/' . $template . '.php';
                // Try to include the template file
                if (file_exists($templateFile)) {
                    require($templateFile);
                } else {
                    // Error with template file
                    require(Utilities::GetMvcRoot() . '/views/Error/BadTemplate.php');
                }
            } else {
                // No template, just show the view directly
                require($this->viewFile);
            }
        } else {
            // Error with the view
            require(Utilities::GetMvcRoot() . '/views/Error/BadView.php');
        }
    }

    // Gets the name of the current controller
    private function GetControllerName() {
        // Parse out the controller's name
        $controllerName = substr(get_class($this), 0, strlen('Controller') * -1);
        return $controllerName;
    }

}
