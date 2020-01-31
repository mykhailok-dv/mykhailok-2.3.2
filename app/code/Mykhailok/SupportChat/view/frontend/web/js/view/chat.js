define([
    'jquery',
    'ko',
    'uiComponent',
    'mykhailokSupportChatModelMessage',
    'mykhailokSupportChatActionSave'
], function ($, ko, Component, messageModel, saveMessage) {
    'use strict';

    return Component.extend({
        messageValue: ko.observable(),
        messages: messageModel().data,

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();

            $(document)
                .on('mykhailok_SupportChat_showChat.namespace_mykhailok_SupportChat', $.proxy(this.showChat, this));
            $(document).on('modalclosed.namespace_mykhailok_SupportChat', $.proxy(this.showChatButton, this));
        },

        /**
         * Init modal from the component HTML. Will be exec after render.
         */
        initModal: function (formElement) {
            this.modal = $(formElement).modal({
                modalClass: 'mykhailok-support-chat-modal',
                buttons: [],
                title: $.mage.__('Message to administrator')
            });
        },

        /**
         * Form has bind data-bind="click".
         */
        saveMessage: function () {
            var self = this,
            data = {
                message: this.messageValue(),
                'form_key': $.mage.cookies.get('form_key'),
                isAjax: 1
            };

            saveMessage(data, this.action, this)
                .done(function () {
                    self.messageValue('');
                }, self)
                .fail(function () {
                    self.hideChat();
                }, self);
        },

        /**
         * Hides the chat block.
         * @see openChatButton.openLinkButton()
         */
        hideChat: function () {
            this.modal.modal('closeModal');
            this.showChatButton();
        },

        /**
         * trigger event to show chat button(s)
         */
        showChatButton: function () {
            $('.mykhailok-support-chat-link.open-chat').trigger('mykhailok_SupportChat_hideChat');
        },

        /**
         * Shows the chat block.
         */
        showChat: function () {
            this.modal.modal('openModal');
        }
    });
});
