Flau-MVC
========

Very simple and basic PHP MVC Framework

### Usage
It's fairly simple to use the framework. As basic setup, you need to create the folders `controllers`, `models`, `views`.
Now upload the file `FlauMVC.inc.php` somwehere (like a `libs` folder).
For a starting point, create an `index.php` file like
```php
<?php
include_once('./libs/FlauMVC.inc.php');

$loader = new FlauMVC\Loader();
$loader->loadFromUrl();
$controller = $loader->createController();
$controller->executeAction($loader->Action);
```
Now the controllers can be created. Simply create an `xxxController.php` in the `controllers` folder. It could look like this:
```php
<?php
class HomeController extends FlauMVC\BaseController {
    // Make sure to call the parent constructor
    public function __construct() {
        parent::__construct();
    }

    function Show() {
        $model = new ShowModel();
        $this->outputView('Show', $model);
    }

    function About() {
        $model = new AboutModel();
        $this->outputView('About', $model);
    }
}
```
As you see, this controller has two methods with it's own view and model: `Show` and `About`
These two `actions` need to have their own subfolder in `models` and `views`, so create them.
For the models, you should now create an `AboutModel.php` and `ShowModel.php` in `views\Home`.
The need to extend `BaseViewModel`. Here's an example how they could look.
```php
class AboutModel extends FlauMVC\BaseViewModel {
    public function __construct() {
        $this->PageTitle = 'About';
    }
}
class ShowModel extends FlauMVC\BaseViewModel {
    public function __construct() {
        $this->PageTitle = 'Welcome';
    }
}
```
As for the views, you should have a `Master.php` in the `views` folder. This contains your basic layout. You can use the `$viewModel` variable to access what you need from it. Now to render the specific view, use `<?php require($this->viewFile); ?>` in the `Master.php`.
Now create the individual view files. So in this sample, you should have `views\Home\About.php` and `views\Home\Show.php`. They contain your individual code for this view. They can also access the `$viewModel` variable.
That's basically it, you now can create more controllers, actions, views and viewmodels.
