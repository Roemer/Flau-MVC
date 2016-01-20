<?php
	/* @var $viewModel \FlauMVC\BaseViewModel */
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $viewModel->PageTitle; ?></title>
        <?php
        // Load and render additional javascripts at the head
        foreach ($viewModel->getAssets(FlauMVC\AssetType::JavaScript, FlauMVC\AssetLocation::Head) as $asset) {
            echo $asset;
        }
        ?>
    </head>
    <body>
        <div>
            <a href="/">Home</a>
            <a href="/<?= \FlauMVC\Utilities::buildUrl('Home', 'About'); ?>">About</a>
        </div>
        <div>
            <?php require($this->viewFile); ?>
        </div>
        <?php
        // Load and render additional javascripts at the bottom
        foreach ($viewModel->getAssets(FlauMVC\AssetType::JavaScript, FlauMVC\AssetLocation::Bottom) as $asset) {
            echo $asset;
        }
        ?>
    </body>
</html>
