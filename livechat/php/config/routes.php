<?php

return array(

    // Installation (comment after running)
    
    'install'          => 'Install:index',
    'install-wizard'   => 'Install:wizard',
    'install-wizard-2' => 'Install:wizard2',
    'install-wizard-3' => 'Install:wizard3',
    'uninstall'        => 'Install:uninstall',
    'uninstall-2'      => 'Install:uninstall2',
    
    // Configuration
    
    'config-get-settings'    => 'Config:getSettings',
    'config-update-settings' => 'Config:updateSettings',
    'config-reset-settings'  => 'Config:resetSettings',
    
    // Authentication
    
    'is-guest-logged-in' => 'Authentication:isGuestLoggedIn',
    'login-guest'        => 'Authentication:loginGuest',
    'login'              => 'Authentication:login',
    'logout'             => 'Authentication:logout',
    'logout-guest'       => 'Authentication:logoutGuest',
    
    // Common
    
    'get-default-avatars' => 'Operator:getDefaultAvatars',
    
    // Guest
    
    'guest-stay-alive'           => 'Guest:stayAlive',
    'guest-update-typing-status' => 'Guest:updateTypingStatus',
    'guest-get-typing-status'    => 'Guest:getTypingStatus',
    'guest-get-online'           => 'Guest:getOnline',
    
    // Operator
    
    'operator-stay-alive'           => 'Operator:stayAlive',
    'operator-update-typing-status' => 'Operator:updateTypingStatus',
    'operator-get-typing-status'    => 'Operator:getTypingStatus',
    'operator-update'               => 'Operator:update',
    'operator-upload-avatar'        => 'Operator:uploadAvatar',
    'operator-list'                 => 'Operator:list',
    'operator-create'               => 'Operator:create',
    'operator-delete'               => 'Operator:delete',
    'operator-get'                  => 'Operator:get',
    'operator-is-online'            => 'Operator:isOnline',
    'operator-online-users'         => 'Operator:getOnlineUsers',
    
    // Message
    
    'message-get-new'                 => 'Message:getNew',
    'message-mark-read'               => 'Message:markMessagesRead',
    'message-get-last'                => 'Message:getLast',
    'message-broadcast'               => 'Message:broadcast',
    'message-send'                    => 'Message:send',
    'message-get-history'             => 'Message:getHistory',
    'message-get-user-history'        => 'Message:getUserHistory',
    'message-query-history'           => 'Message:queryHistory',
    'message-clear-history'           => 'Message:clearHistory',
    'message-operator-get-new'        => 'Message:operatorGetNew',
    'message-operator-guest-get-last' => 'Message:operatorGuestGetLast',
    
    // Canned message
    
    'canned-message-create' => 'CannedMessage:create',
    'canned-message-get'    => 'CannedMessage:get',
    'canned-message-list'   => 'CannedMessage:list',
    'canned-message-update' => 'CannedMessage:update',
    'canned-message-delete' => 'CannedMessage:delete',
    
    // Contact
    
    'contact' => 'Contact:contact',
    
    // Widget
    
    'widget-iframe-content'     => 'Widget:iframeContent',
    'widget-mobile'             => 'Widget:mobileView',
    'widget-init.js'            => 'Widget:init',
    'widget-custom-style.css'   => 'Widget:customStyle',
    'widget-theme-style.css'    => 'Widget:themeStyle',
    
    // Admin
    
    'admin'            => 'Admin:index',
    'admin-templates'  => 'Admin:templates',
    'admin-logs'       => 'Admin:logs',
    'admin-clear-logs' => 'Admin:clearLogs',
    'widget-test'      => 'Admin:widgetTest'
);

?>
