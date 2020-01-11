define(
    [
        'uiComponent',
        'ko',
        'jquery'
    ], function (component, ko, $) {
        'use strict';

        /**
         * @public
         * @param {String} time
         * @param {String} text
         * @param {Boolean} isAdmin
         */
        var Message = function (time, text, isAdmin) {
            var self = this;

            self.time = time;
            self.text = text;
            self.isAdmin = isAdmin;
        };

        return component.extend({
            /** Data. */
            messages: ko.observableArray([]),

            /**
             * Constructor.
             * @public
             */
            initialize: function () {
                this._super();
                this.element = $(this.elementSelector);
                this.element.on(
                    'mykhailok_SupportChat_chat-messages-added.namespace_mykhailok_SupportChat',
                    this.addMessages.bind(this)
                );
            },

            /**
             * @public
             */
            addMessages: function (element, eventData) {
                eventData.messages.forEach(function (message) {
                    this.messages.push(
                        new Message(
                            this._getConvertedTime(message.time),
                            message.text,
                            eventData.isAdmin
                        )
                    );
                }, this);
            },

            /**
             * @private
             * @return {String}
             */
            _getConvertedTime: function (timestamp) {
                var date = new Date(timestamp * 1000),
                    months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                return date.getDate() + ' ' +
                    months[date.getMonth()] + ' ' +
                    date.getFullYear() + ' ' +
                    date.getHours() + ':' +
                    date.getMinutes() + ':' +
                    date.getSeconds();
            }
        });
    });
