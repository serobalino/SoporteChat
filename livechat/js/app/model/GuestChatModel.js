//==============================================================================
//
//  Chat model
//
//==============================================================================

(function(app, $, config, _)
{
    var GuestChatModel = app.GuestChatModel = Backbone.Model.extend({

        defaults : {

            name : 'anonymous',
            mail : ''
        },

        operatorsCache : {},

        lastMessages : [],

        lastTypingUpdate : 0,

        initialize : function()
        {
            // Handle chatting features

            this.on('login:success', this.manageConnection, this);

            this.on('messages:new', this.storeMessages,       this);
            this.on('messages:new', this.confirmMessagesRead, this);
        },

        autoLogin : function()
        {
            // Check if user is already logged in

            var _this = this;

            $.post(config.isLoggedInPath, { info : JSON.stringify(config.info) }, function(data)
            {
                if(data.success)
                {
                    // Store the login data

                    _this.set({ name : data.name, mail : data.mail, image : data.image });

                    // Notify success

                    _this.trigger('login:success');

                    // Read previous messages if any

                    $.get(config.lastMessagesPath, function(data)
                    {
                        if(data.success && data.messages.length > 0)
                        {
                            _this.trigger('messages:last', data.messages);
                        }
                    });
                }
                else
                {
                    // Notify about need to log in again

                    _this.trigger('login:login');
                }
            });
        },

        login : function(input)
        {
            var _this = this;

            input.info = JSON.stringify(config.info);

            $.post(config.loginPath, input, function(data)
            {
                if(data.success)
                {
                    // Store the login data

                    _this.set({ name : input.name, mail : input.mail, image : input.image });

                    // Notify success

                    _this.trigger('login:success');
                }
                else
                {
                    // Notify about need to log in again

                    _this.trigger('login:error');
                }
            });
        },

        logout : function()
        {
            // Stop connection management

            this.stopConnectionManagement();

            // Inform the operator

            var _this = this;

            this.sendMessage(new app.MessageModel({ body : '[ user has closed the chat ]' }), function()
            {
                // Send a logout request

                $.post(config.logoutPath, function(data)
                {
                    if(data && data.success)
                    {
                        // Notify about successful log-out

                        _this.trigger('logout:success');
                    }
                    else
                    {
                        // Notify about log-out error

                        _this.trigger('logout:error');
                    }
                });
            });

            // Clear messages cache

            this.lastMessages = [];

            // Notify about logging out

            this.trigger('logout:init');
        },

        checkOperators : function(callback)
        {
            // Check if there's any operator on-line

            var _this = this;

            var p = $.get(config.isOperatorOnlinePath, function(data)
            {
                if(data.success)
                {
                    // Notify about online operator(s)

                    _this.trigger('operators:online');
                }
                else
                {
                    // Notify about no operator(s)

                    _this.trigger('operators:offline');
                }

            });

            if(callback) p.always(callback);
        },

        keepAlive : function(callback)
        {
            // Send keep-alive request

            var p = $.get(config.keepAlivePath);

            if(callback) p.always(callback);
        },

        updateTypingStatus : function()
        {
            // Get operator's ID

            var operatorId = this.lastOperator && this.lastOperator.id;

            if(operatorId)
            {
                // Send the request only once per given amount of time

                var time = (new Date()).getTime();

                if(this.lastTypingUpdate + GuestChatModel.POLLING_INTERVAL < time)
                {
                    this.lastTypingUpdate = time;

                    // Send typing status update request

                    $.post(config.updateTypingStatusPath, { secondUserId : operatorId, status : true });
                }
            }
        },

        getTypingStatus : function(callback)
        {
            // Get operator's ID

            var operatorId = this.lastOperator && this.lastOperator.id;

            if(operatorId)
            {
                // Get typing status

                var _this = this;

                var p = $.post(config.getTypingStatusPath, { ids : [ operatorId ] }, function(data)
                {
                    if(data.success && data.results[operatorId])
                    {
                        _this.trigger('operator:typing');
                    }

                });

                if(callback) p.always(callback);
            }
            else
            {
                if(callback) callback();
            }
        },

        getMessages : function(callback)
        {
            // Poll new messages data

            var _this = this;

            var p = $.get(config.newMessagesPath, function(data)
            {
                // Check if there are any new messages

                if(data.length > 0)
                {
                    // Collect operator(s) info

                    _this.loadOperatorsData(data, function()
                    {
                        // Notify about new messages

                        data.authorType = 'operator';

                        _this.trigger('messages:new', data);
                    });
                }

            });

            if(callback) p.always(callback);
        },

        confirmMessagesRead : function(data)
        {
            // Get first and last message IDs

            var data = {

                firstId : data[0].id,
                lastId  : data[data.length - 1].id
            };

            // Send the confirmation request

            $.post(config.markMessagesReadPath, data);
        },

        storeMessages : function(messages)
        {
            // Prepare the messages

            _.each(messages, function(message)
            {
                if(!message.datetime && message.time)
                {
                    message.datetime = message.time.getTime();
                }
            });

            // Save the messages

            this.lastMessages = this.lastMessages.concat(messages);

            // Store in the cookie
            /*
            var date    = new Date();
            var minutes = 10;

            date.setTime(date.getTime() + minutes * 60 * 1000);

            $.cookie('customer-chat-messages', JSON.stringify(this.lastMessages), { expires : date });*/
        },

        storeOperator : function(operator)
        {
            this.lastOperator = this.operatorsCache[operator.id] = operator;

            // Save the cookie
            /*
            var date    = new Date();
            var minutes = 15;

            date.setTime(date.getTime() + minutes * 60 * 1000);

            $.cookie('customer-chat-operators', JSON.stringify(this.operatorsCache), { expires : date });*/
        },

        loadOperatorsData : function(messages, callback)
        {
            var _this = this;

            var loadCount = 0;

            // Check if there's any message from a not known operator

            for(var i = 0; i < messages.length; i++)
            {
                var message = messages[i];

                if(!this.operatorsCache[message.from_id])
                {
                    // Load operator's info

                    loadCount++;

                    $.post(config.getOperatorPath, { id : message.from_id })

                        .success(function(data)
                        {
                            if(data.success)
                            {
                                // Store the data

                                _this.storeOperator(data.user);
                            }
                        })

                        .always(function()
                        {
                            loadCount--;

                            if(loadCount <= 0)
                            {
                                // Finish the operation

                                callback();
                            }
                        })
                    ;
                }
            }

            if(loadCount <= 0)
            {
                // Finish the operation

                callback();
            }
        },

        getOperatorName : function(id)
        {
            return this.operatorsCache[id] && this.operatorsCache[id].name;
        },

        sendMessage : function(message, callback)
        {
            // Prepare data

            var input = {

                body : message.get('body')
            };

            // Send message to the server

            var _this = this;

            $.post(config.sendMessagePath, input, function(data)
            {
                if(data.success)
                {
                    // Notify success

                    _this.trigger('messages:sent');
                }
                else
                {
                    // Notify error

                    _this.trigger('messages:sendError');
                }

                if(callback) callback(data);
            });

            // Store the message

            this.storeMessages([ message.attributes ]);
        },

        manageConnection : function()
        {
            // Clear previous interval

            this.stopConnectionManagement();

            // Reset request statuses

            this.initRequestsStatus();

            // Start connection interval

            this.connectionTimer = setInterval(

                $.proxy(this._manageConnection, this),

                GuestChatModel.POLLING_INTERVAL
            );

            // Send initial requets

            this._manageConnection();
        },

        _manageConnection : function()
        {
            // Don't make more requests before previous ones has completed

            if(!this.prevRequestsComplete())
            {
                return;
            }

            // Reset request statuses

            this.resetRequestsStatus();

            var _this = this;

            // New messages polling

            this.getMessages(function()
            {
                _this.requestsStatus.getMessages = true;
            });

            // Keeping connection alive

            this.keepAlive(function()
            {
                _this.requestsStatus.keepAlive = true;
            });

            // Checking typing status

            this.getTypingStatus(function()
            {
                _this.requestsStatus.getTypingStatus = true;
            });

            // Checking operator's availability

            this.checkOperators(function()
            {
                _this.requestsStatus.checkOperators = true;
            });
        },

        prevRequestsComplete : function()
        {
            // Reset if too much time passed

            if(Date.now() - this.requestInitTime > GuestChatModel.REQUEST_STATUS_RESET_TIMEOUT)
            {
                this.initRequestsStatus();

                return true;
            }

            for(var k in this.requestsStatus)
            {
                if(!this.requestsStatus[k])
                {
                    return false;
                }
            }

            this.requestInitTime = Date.now();

            return true;
        },

        initRequestsStatus : function()
        {
            this.requestInitTime = Date.now();

            this.requestsStatus = {

                getMessages     : true,
                keepAlive       : true,
                getTypingStatus : true,
                checkOperators  : true
            };
        },

        resetRequestsStatus : function()
        {
            for(var k in this.requestsStatus)
            {
                this.requestsStatus[k] = false;
            }
        },

        stopConnectionManagement : function()
        {
            if(this.connectionTimer) clearInterval(this.connectionTimer);
        }
    },
    {
        POLLING_INTERVAL             :  5000,
        REQUEST_STATUS_RESET_TIMEOUT : 20000
    });

})(window.Application, jQuery, window.chatConfig, _);
