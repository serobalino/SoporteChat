//==============================================================================
//
//  Select canned message view
//
//==============================================================================

(function(app, config, $)
{
    var SelectCannedMessageView = app.SelectCannedMessageView = Backbone.View.extend({
    
        events : {
            
            'mousedown .customer-chat-content-canned-message' : 'selectMessage'
        },
        
        initialize : function()
        {
            this.listenTo(this.model, 'change', this.render);
        },
        
        render : function()
        {
            // Clear the view
            
            this.$el.html(app.template.selectCannedMessageContent);
            
            this.$messages = this.$('.messages');
            
            // Display available messages
            
            var messages = this.model.get('messages');
            
            for(var i = 0; i < messages.length; i++)
            {
                var $message = $('<a class="customer-chat-content-canned-message"></a>');
                
                var finalBody = this.model.getParametrizedMessage(messages[i].body);

                var displayName = messages[i].name;
                var displayBody = finalBody;
                
                if(displayBody.length > SelectCannedMessageView.DISPLAY_BODY_MAX_LENGTH)
                {
                    displayBody = displayBody.slice(0, SelectCannedMessageView.DISPLAY_BODY_MAX_LENGTH) + '...';
                }
                
                if(displayName.length > SelectCannedMessageView.DISPLAY_NAME_MAX_LENGTH)
                {
                    displayName = displayName.slice(0, SelectCannedMessageView.DISPLAY_NAME_MAX_LENGTH) + '...';
                }
                
                $message.html('<b>' + displayName + '</b> (<i>' + displayBody + '</i>)').data('message', finalBody);
                
                this.$messages.append($message);
            }
            
            // Initialize the scroller
            
            this.$el.find('.canned-messages-wrapper').mCustomScrollbar();
            
            $(window).resize();
            
            return this;
        },
        
        selectMessage : function(e)
        {
            var $message = $(e.currentTarget);
            var message  = $message.data('message');
            
            this.$messages.children().removeClass('selected');
            $message.addClass('selected');
            
            this.selected = message;
        }
    },
    {
        DISPLAY_NAME_MAX_LENGTH : 50,
        DISPLAY_BODY_MAX_LENGTH : 100
    });

})(window.Application, chatConfig, jQuery);