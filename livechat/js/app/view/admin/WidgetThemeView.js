//==============================================================================
//
//  Widget theme view
//
//==============================================================================

(function(app, config, $)
{
    app.WidgetThemeView = Backbone.View.extend({
        
        events : {
        
            'click  #customer-chat-widget-theme-save' : 'save',
            'change #inputWidgetTheme'                : 'updatePreview'
        },
        
        inputs : null,
        
        syncing : false,
        
        initialize : function()
        {
            // Initialize models
            
            this.model = app.model.uiSettings;
            
            // Cache view elements
            
            this.$inputs      = this.$('*[name]');
            this.$widgetTheme = this.$('#inputWidgetTheme');
            this.$preview     = this.$('#widget-theme-preview');
            this.$save        = this.$('#customer-chat-widget-theme-save');
            this.$loading     = this.$('.customer-chat-content-loading-icon').hide();
            
            this.$inputs.blur($.proxy(this.validate, this));
            
            // Handle rendering
            
            this.model.on('change',  this.render,  this);
            this.model.on('request', this.disable, this);
            this.model.on('sync',    this.enable,  this);
            
            this.render();
        },
        
        save : function()
        {
            var attributes = {};

            this.$inputs.each(function()
            {
                var $el = $(this);

                if($el.attr('type') == 'checkbox')
                {
                    attributes[$el.attr('name')] = $el.is(':checked') ? 'true' : 'false';
                }
                else
                {
                    attributes[$el.attr('name')] = $el.val();
                }
            });

            if(!this.syncing)
            {
                this.model.save(attributes);
                this.model.once('sync', $.proxy(this.onSave, this));
            }
        },

        onSave : function()
        {
            // Refresh the whole model so that settings tab is up-to-date

            this.model.fetch();
        },
        
        render : function()
        {
            var _this = this;
            
            this.$inputs.each(function()
            {
                var $el = $(this);
                
                // Display the model value in the input element
                
                if($el.attr('type') == 'checkbox')
                {
                    $el.attr('checked', _this.model.get($el.attr('name')) == 'true');
                }
                else
                {
                    $el.val(_this.model.get($el.attr('name')));
                }
            });
            
            this.updatePreview();
        },
        
        updatePreview : function()
        {
            // Update theme's preview image
            
            var themePath = this.$widgetTheme.val();
            
            if(themePath)
            {
                var imagePath = config.rootPath + themePath + '/preview.png';
                
                this.$preview.attr('src', imagePath);
            }
        },
        
        disable : function()
        {
            this.syncing = true;
            
            this.$save.addClass('button-disabled');
            this.$loading.fadeIn();
        },
        
        enable : function()
        {
            var _this = this;
            
            setTimeout(function()
            {
                _this.$save.removeClass('button-disabled');
                _this.$loading.fadeOut();
                
                _this.syncing = false;
            
            }, 500);
        }
    });

})(window.Application, window.chatConfig, jQuery);