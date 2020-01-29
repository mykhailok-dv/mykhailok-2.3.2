define([
    'ko',
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data'

], function (ko, $, Component, customerData) {
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

    return Component.extend({
        defaults: {
            data: ko.observableArray([]),
            messagesIndexes: []
        },
        supportChat: customerData.get('support-chat'),

        /** @inheritdoc */
        initialize: function () {
            this._super();

            this.renderSavedMessages();
            this.initSubscribe();
        },

        /**
         * These method do update this.data variable as array of Message objects that includes all messages from
         * the Browser::Localstorage.mage-cache-storage.support-chat.messages[]
         * @public
         */
        updateRenderedMessages: function () {
            if (typeof this.supportChat().messages !== 'object') {
                return;
            }

            Object.entries(this.supportChat().messages).forEach(function (messageArray) {
                var index = messageArray.shift(),
                    message = messageArray.shift();

                if (this.messagesIndexes.includes(index)) {
                    return;
                }

                this.messagesIndexes.push(index);
                this.data.push(
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
         * Alias for updateMessages(). It should be runs every time when page was loaded.
         * @see this.updateMessages()
         */
        renderSavedMessages: function () {
            this.updateRenderedMessages();
        },

        /**
         * Allows to run updateRenderedMessages() method every time when messages in the LocalStorage
         * will be updated.
         */
        initSubscribe: function () {
            this.supportChat.subscribe(function () {
                this.updateRenderedMessages();
            }, this);
        }
    });
});
