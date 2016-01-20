<?php

use models\Home as M;

class HomeController extends FlauMVC\BaseController {

    // Make sure to call the parent constructor
    public function __construct() {
        parent::__construct();
    }

    function Show() {
        $model = new M\ShowModel();
        $this->outputView('Show', $model);
    }

    function About() {
        $model = new M\AboutModel();
        $this->outputView('About', $model);
    }
}
