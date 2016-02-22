<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Database error</title>

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

            <div class="header">Database error</div>

            <div class="content">
                
                <!-- Message -->
                
                <p class="intro">
                    The application couldn't connect to your database. Please make sure that database settings are correct.
                    
                    <br><br>
                    
                    <strong>
                        If your database — <?php echo $vars['config']['dbName'] ?> — doesn't exist yet, please create it
                        manually using your favorite database administration tool (e. g. <i>phpMyAdmin</i>) and come back after it is done.
                    </strong>
                    
                    <br><br>
                    
                    Error message returned from the database was:
                </p>
                
                <p class="error">
                    <?php echo $vars['message'] ?>
                </p>
                
                <!-- Actions -->

                <div class="row buttons">
                    <a class="btn" href="<?php echo $app->path('Install:wizard') ?>">
                        Sign in as administrator and correct your database settings
                        <i class="icon-circle-arrow-right icon-white"></i>
                    </a>
                </div>

            </div>
        </div>

    </body>
</html>
