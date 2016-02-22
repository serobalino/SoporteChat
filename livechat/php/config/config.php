<?php

require_once ROOT_DIR . '/lib/timezone.php';

$parameters = include ROOT_DIR . '/config/parameters.php';

$config = Utils::arrayMergeRecursive(array(

    // Environment info

    'env' => 'prod', // 'dev' / 'prod'

    // Other (do not modify manually)

    'services' => array(

        'configuration' => array(

            'appSettingsFile' => 'app.settings.php'
        ),

        'memory' => array(

            'file' => 'data/memory.dat'
        ),

        'logger' => array(

            'file'        => 'data/log.dat',
            'logErrors'   => true,
            'logWarnings' => false,
            'logInfos'    => false
        ),

        'mailer' => array(

            'smtp'       => false,
            'smtpSecure' => 'ssl',
            'smtpHost'   => '',
            'smtpUser'   => '',
            'smtpPass'   => '',
            'smtpPort'   => 465
        )
    ),

    'dbType' => 'mysql',

    'avatarImageSize' => 40,

    'defaultSettings' => array(

        'widgetTheme'            => 'widget-themes/original',
        'notificationIcon'       => 'img/notification.png',
        'primaryColor'           => '#36a9e1',
        'secondaryColor'         => '#86C953',
        'labelColor'             => '#ffffff',
        'hideWhenOffline'        => false,
        'autoShowWidget'         => 'true',
        'autoShowWidgetAfter'    =>   10,
        'contactMail'            => 'admin@domain.com',
        'loadingLabel'           => 'Loading...',
        'loginError'             => 'Login error',
        'chatHeader'             => 'Talk to us',
        'startInfo'              => 'Please fill the following form to start the chat',
        'askForMail'             => 'true',
        'headerHeight'           =>  55,
        'widgetWidth'            => 370,
        'widgetHeight'           => 411,
        'widgetOffset'           =>  50,
        'mobileBreakpoint'       => 550,
        'maxConnections'         =>   5,
        'messageSound'           => 'audio/default.mp3',
        'startLabel'             => 'Start',
        'backLabel'              => 'Back',
        'initMessageBody'        => 'Hello, how may I help you?',
        'initMessageAuthor'      => 'Operator',
        'chatInputLabel'         => 'Write your question',
        'timeDaysAgo'            => 'day(s) ago',
        'timeHoursAgo'           => 'hour(s) ago',
        'timeMinutesAgo'         => 'minute(s) ago',
        'timeSecondsAgo'         => 'second(s) ago',
        'offlineMessage'         => 'Operator went off-line',
        'toggleSoundLabel'       => 'Sound effects',
        'toggleScrollLabel'      => 'Auto-scroll',
        'toggleEmoticonsLabel'   => 'Emoticons',
        'toggleAutoShowLabel'    => 'Auto-show',
        'toggleFullscreenLabel'  => 'Toggle fullscreen',
        'endChatLabel'           => 'End the chat',
        'endChatConfirmQuestion' => 'Are you sure?',
        'endChatConfirm'         => 'Yes',
        'endChatCancel'          => 'Cancel',
        'contactHeader'          => 'Contact us',
        'contactInfo'            => 'All operators are off-line. Use the below form to send us an e-mail with your question.',
        'contactNameLabel'       => 'Your name',
        'contactMailLabel'       => 'Your e-mail',
        'contactQuestionLabel'   => 'Your question',
        'contactSendLabel'       => 'Send',
        'contactSuccessHeader'   => 'Message sent',
        'contactSuccessMessage'  => 'Your question has been sent. Thank you!',
        'contactErrorHeader'     => 'Error',
        'contactErrorMessage'    => 'There was an error sending your question'
    )

), $parameters);

// Generate connection strings

$config['dbConnectionRaw_mysql'] = 'mysql:host=' . $config['dbHost'] . ';port=' . $config['dbPort'];
$config['dbConnection_mysql']    = 'mysql:dbname=' . $config['dbName'] . ';host=' . $config['dbHost'] . ';port=' . $config['dbPort'];

// Used connection strings

$config['dbConnectionRaw'] = $config['dbConnectionRaw_' . $config['dbType']];
$config['dbConnection']    = $config['dbConnection_'    . $config['dbType']];

return $config;

?>
