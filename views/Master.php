<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $viewModel->PageTitle; ?></title>
        <!-- Additional User-Defined Scripts -->
        <?php
        if (!empty($viewModel->IncludeScripts)) {
            foreach ($viewModel->IncludeScripts as $scriptPath) {
                echo '<script src="' . $scriptPath . '" type="text/javascript"></script>';
            }
        }
        ?>
    </head>
    <body>
        <div>
            <a href="/mvc">Home</a> 
            <a href="/mvc/index.php?controller=Home&action=About">About</a>
        </div>
        <div>
            <?php require($this->viewFile); ?>
        </div>
    </body>
</html>
