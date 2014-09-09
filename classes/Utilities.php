<?php

class Utilities {

    static function RequireAllFilesFromFolder($folder) {
        foreach (glob("{$folder}/*.php") as $filename) {
            require_once $filename;
        }
    }

    static function RedirectToAction($immediatelyExit, $controller, $action, array $additionalData = NULL) {
        $url = GetUrl($controller, $action, $additionalData);
        header('Location: ' . $url);
        if ($immediatelyExit) {
            exit();
        }
    }

    static function GetMvcRoot() {
        return $_SERVER['DOCUMENT_ROOT'] . '/mvc';
    }

}
