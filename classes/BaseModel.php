<?php

abstract class BaseModel {

    public $PageTitle;
    public $IncludeScripts = array();

    public function RegisterScript($scriptPath) {
        array_push($this->IncludeScripts, $scriptPath);
    }

}