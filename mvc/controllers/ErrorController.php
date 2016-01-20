<?php

use models\Error as M;

class ErrorController extends FlauMVC\BaseController {

    // Make sure to call the parent constructor
    public function __construct() {
        parent::__construct();
    }

    // Bad URL request error
    function BadUrl() {
        $model = new M\BadUrlModel();
        $this->outputView('BadUrl', $model);
    }

}
