//==============================================================================
//
//  Main window view
//
//==============================================================================

(function(app, $, config)
{
    var WindowView = app.WindowView = Backbone.View.extend({

        initialize : function()
        {
            // Initialize models

            this.settings = app.model.settings;

            // Create sub views

            var user = this.user = app.model.user;

            if(user.hasRole('ADMIN'))
            {
                this.settingsView    = new app.SettingsView   ({ el : this.$('.customer-chat-tab-content-settings-ui') });
                this.widgetThemeView = new app.WidgetThemeView({ el : this.$('.customer-chat-tab-content-widget-theme') });
                this.logsView        = new app.LogsView       ({ el : this.$('#customer-chat-admin-logs') });
            }

            this.chatTabView        = new app.ChatTabView       ({ el : this.$('#customer-chat-admin-chat') });
            this.operatorsView      = new app.OperatorsView     ({ el : this.$('#customer-chat-operators-tab') });
            this.cannedMessagesView = new app.CannedMessagesView({ el : this.$('#customer-chat-canned-messages-tab') });
            this.historyView        = new app.HistoryView       ({ el : this.$('#customer-chat-history') });
            this.tabsView           = new app.TabsView          ({ el : this.$('#customer-chat-admin-settings') });
            this.menuView           = new app.MenuView          ({ el : this.el, windowView : this });

            if(!user.hasRole('ADMIN'))
            {
                this.tabsView.removeTab(0); // Settings tab
                this.tabsView.removeTab(1); // Widget theme tab
                this.tabsView.removeTab(1); // Canned messages tab

                this.$('.customer-chat-tab-button.operators').html('Edit profile');
            }

            // -----

            if(this.user.hasRole('OPERATOR'))
            {
                // Handle "new message" indicator

                this.titleText = document.title;

                this.listenTo(this.chatTabView, 'talks.read',   this.stopMsgIndicator);
                this.listenTo(this.chatTabView, 'talks.unread', this.startMsgIndicator);
            }

            // Keep updating the scroller when necessary

            this.$('#customer-chat-admin-settings .customer-chat-content-messages')

                .bind('show', function()
                {
                    $(window).trigger('resize');
                })

                .not('#customer-chat-history .customer-chat-content-messages')

                .mCustomScrollbar()
            ;

            // Overwrite the standard alert() function

            window.alert = function(message)
            {
                app.view.dialogs.message('Alert', message);
            };

            // Show the window

            this.$el.animate({ opacity : 1 }, { duration : 'slow', complete : this.checkInstall });

            // Handle global events

            this.listenTo(app, 'history.search', this.showHistory);
        },

        showHistory : function()
        {
            // Show the settings tab

            this.menuView.showSettings();

            // Show the history tab

            this.tabsView.showTab(this.user.hasRole('OPERATOR') ? 2 : 4);
        },

        startMsgIndicator : function()
        {
            if(!this.msgIndicatorTimer)
            {
                this.msgIndicatorTimer = setInterval($.proxy(this.indicateMsg, this), WindowView.MSG_INDICATOR_INTERVAL);
            }
        },

        stopMsgIndicator : function()
        {
            if(this.msgIndicatorTimer)
            {
                clearInterval(this.msgIndicatorTimer);

                document.title = this.titleText;

                delete this.msgIndicatorTimer;
            }

            // Hide desktop notifications

            app.service.notify.closeAll();
        },

        indicateMsg : function()
        {
            document.title = document.title !== '!' ? '!' : this.titleText;
        },

        checkInstall : function()
        {
            // Prompt to correct installation if failed

            if(!(config.installStatus.validConfig && config.installStatus.validDb))
            {
                if(app.model.user.hasRole('ADMIN'))
                {
                    app.view.dialogs.confirm(

                        'Incorrect installation',
                        app.template.invalidInstallDialogContent,

                        {
                            'Edit configuration' : function()
                            {
                                // Redirect to the configuration editing page

                                (document.location || window.location).href = config.installWizardPath;
                            }
                        }
                    );
                }
                else
                {
                    app.view.dialogs.message('Please install first', 'The application is not yet installed, please log in as administrator and install it before using');
                }
            }

            // Prompt for installation if not already done

            if(!config.ui.installed)
            {
                if(app.model.user.hasRole('ADMIN'))
                {
                    app.view.dialogs.confirm(

                        'Install',
                        app.template.installDialogContent,

                        {
                            'Install' : function()
                            {
                                // Redirect to the install path

                                (document.location || window.location).href = config.installPath;
                            }
                        }
                    );
                }
                else
                {
                    app.view.dialogs.message('Please install first', 'The application is not yet installed, please log in as administrator and install it before using');
                }
            }
        }
    },
    {
        MSG_INDICATOR_INTERVAL : 1000
    });

})(window.Application, jQuery, window.chatConfig);
