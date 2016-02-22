//==============================================================================
//
//  Application startup
//
//==============================================================================

jQuery(function($)
{
    var config = window.chatConfig;
    
    // Preload resources
    
    $.get(config.templatesPath, function(data)
    {
        var templates = $(data);
        
        // -----
        
        var app = window.Application;
        
        // Initialize services
        
        app.service.soundPlayer = new app.SoundPlayer();

        // Initialize templates
        
        app.template.message = templates.find('#message').html();
        
        // Initialize models
        
        app.model.settings = new app.GuestSettingsModel();
        app.model.chat     = new app.GuestChatModel();
        
        // Initialize views

        app.view.widget = new app.WidgetView({ el : '#customer-chat-widget', model : app.model.chat });

        // Initialize API

        window.phpLiveChat = new app.WidgetFacade();

        new app.PostMessageApiAdapter(window.phpLiveChat);
    });
});