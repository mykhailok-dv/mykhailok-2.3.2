define([
    'uiComponent',
    'ko',
    'jquery'
], function (component, ko, $) {
    'use strict';

    /**
     * @public
     * @param {String} time
     * @param {String} text
     * @param {String} name
     * @param {Boolean} isAdmin
     */
    var Message = function (time, text, name, isAdmin) {
        var self = this;

        self.time = time;
        self.text = text;
        self.name = name;
        self.isAdmin = isAdmin;
    };

    return component.extend({
        /** Data. */
        messages: ko.observableArray([]),
        messagesIndexes: [],

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
            var messages = Object.entries(eventData.messages);

            messages.forEach(function (messageArray) {
                var index = messageArray.shift(),
                    message = messageArray.shift();

                if (this.messagesIndexes.includes(index)) {
                    return;
                }

                this.messagesIndexes.push(index);
                this.messages.push(
                    new Message(
                        message.time,
                        message.text,
                        message.authorName,
                        message.authorType === 'USER_TYPE_ADMIN'
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
