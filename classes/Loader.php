<?php

class Loader {

    public $ControllerName;
    public $ControllerClass;
    public $Action;
    public $UrlValues;

    // Read out the parameters from the url
    public function LoadFromUrl() {
        $this->UrlValues = $_GET;

        // Read out the controller
        if ($this->UrlValues['controller'] == "") {
            // Default controller
            $controllerName = "Home";
        } else {
            $controllerName = $this->UrlValues['controller'];
        }

        // Read out the action
        if ($this->UrlValues['action'] == "") {
            // Default action
            $action = 'Show';
        } else {
            $action = $this->UrlValues['action'];
        }

        $this->LoadDirectly($controllerName, $action);
    }

    // Directly load a controller by name
    public function LoadDirectly($controllerName, $action = NULL) {
        $this->UrlValues = $_GET;
        $this->ControllerName = ucfirst(strtolower($controllerName));
        $this->ControllerClass = $this->ControllerName . "Controller";
        $this->Action = ucfirst($action);
    }

    // Create the appropriate controller
    public function CreateController() {
        // Check if the requested controller class file exists and require it if so
        if (file_exists(Utilities::GetMvcRoot() . '/controllers/' . $this->ControllerClass . '.php')) {
            require(Utilities::GetMvcRoot() . '/controllers/' . $this->ControllerClass . '.php');
        } else {
            return $this->CreateErrorController();
        }

        // Check if the controller-class exists
        if (class_exists($this->ControllerClass)) {
            $parents = class_parents($this->ControllerClass);
            // Check that it extends the BaseController
            if (in_array("BaseController", $parents)) {
                // Check if it contains the action
                if (method_exists($this->ControllerClass, $this->Action)) {
                    // Create the controller
                    return new $this->ControllerClass();
                } else {
                    // Bad action/method error
                    return $this->CreateErrorController();
                }
            } else {
                // Bad controller error
                return $this->CreateErrorController();
            }
        } else {
            // Bad controller error
            return $this->CreateErrorController();
        }
    }

    private function CreateErrorController() {
        require(Utilities::GetMvcRoot() . '/controllers/ErrorController.php');
        $this->Action = 'BadUrl';
        return new ErrorController();
    }

}
