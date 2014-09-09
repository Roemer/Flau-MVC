<?php

use models\Home as M;

class HomeController extends BaseController {

    // Make sure to call the parent constructor
    public function __construct() {
        parent::__construct();
    }

    function Show() {
        $model = new M\ShowModel();
        $this->OutputView('Show', $model);
    }

    function About() {
        $model = new M\AboutModel();
        $this->OutputView('About', $model);
    }
}
