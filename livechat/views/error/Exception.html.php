<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Error</title>

        <!-- Fonts -->

        <?php echo $app->renderView('blocks/fonts.html'); ?>

        <!-- Styles -->

        <link rel="stylesheet" href="<?php echo $app->asset('css/jquery.mCustomScrollbar.css') ?>" />
        <link rel="stylesheet" href="<?php echo $app->asset('css/framework.css') ?>" />
        <link rel="stylesheet" href="<?php echo $app->asset('css/main.css') ?>" />
        <link rel="stylesheet" href="<?php echo $app->asset('css/admin.css') ?>" />
        <link rel="stylesheet" href="<?php echo $app->asset('css/bootstrap.css') ?>" />
    </head>
    <body class="install-wizard">

        <div class="panel">

            <div class="header">Error</div>

            <div class="content">
                
                <!-- Message -->
                
                <p class="intro">
                    It seems that something went wrong.
                    
                    <br><br>
                    
                    Don't worry though — here is the error message that can help you find the problem:
                </p>
                
                <p class="error">
                    <?php echo $vars['message'] ?>
                </p>

            </div>
        </div>

    </body>
</html>
