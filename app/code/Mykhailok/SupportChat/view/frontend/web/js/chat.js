define([
    'jquery',
    'mage/mage',
    'Magento_Ui/js/modal/modal'
], function ($) {
    'use strict';

    /**
     * @property mykhailokSupportChat.chat
     */
    $.widget('mykhailokSupportChat.chat', {
        options: {
            openLinkButtonSelector: '.mykhailok-support-chat-link.open-chat',
            closeChatButtonSelector: '#mykhailok-support-chat-close-button',
            submitMessageButtonSelector: '#mykhailok-support-chat-submit-button',
            form: '#mykhailok-support-chat',
            chatContainerSelector: '',
            action: ''
        },

        /**
         * @private
         */
        _create: function () {
            this.modal = $(this.element).modal({
                modalClass: 'mykhailok-support-chat-modal',
                buttons: [],
                title: $(this.element).find('h3').remove().html()
            });

            $(document)
                .on('mykhailok_SupportChat_showChat.namespace_mykhailok_SupportChat', $.proxy(this.showChat, this));
            $(this.options.closeChatButtonSelector)
                .on('click.namespace_mykhailok_SupportChat', $.proxy(this.hideChat, this));
            $(this.element)
                .on('modalclosed.namespace_mykhailok_SupportChat', $.proxy(this.showChatButton, this));
            $(this.options.submitMessageButtonSelector)
                .on('click.namespace_mykhailok_SupportChat', $.proxy(this.submitMessage, this));
            this.chatContainer = $(this.options.chatContainerSelector);
        },

        /**
         * Hides the chat block.
         * @see openChatButton.openLinkButton()
         */
        hideChat: function () {
            $(this.element).data('mage-modal').closeModal();
            this.showChatButton();
        },

        /**
         * trigger event to show chat button(s)
         */
        showChatButton: function () {
            $(this.options.openLinkButtonSelector).trigger('mykhailok_SupportChat_hideChat');
        },

        /**
         * @private
         */
        _destroy: function () {
            $(document).off('mykhailok_SupportChat_showChat.namespace_mykhailok_SupportChat');
            $(this.options.closeChatButtonSelector).off('click.namespace_mykhailok_SupportChat');
            $(this.element).off('modalclosed.namespace_mykhailok_SupportChat');
            $(this.options.submitMessageButtonSelector).off('click.namespace_mykhailok_SupportChat');
        },

        /**
         * Shows the chat block.
         */
        showChat: function () {
            $(this.element).addClass('active');
            $(this.element).data('mage-modal').openModal();
        },

        /**
         * Submit form action.
         */
        submitMessage: function (e) {
            if (!this.validateForm(e)) {
                return;
            }

            this.ajaxSubmit(e);
        },

        /**
         * Validate request form
         */
        validateForm: function (e) {
            return $(e.currentTarget.form).valid();
        },

        /**
         * Submit request via AJAX. Add form key to the post data.
         */
        ajaxSubmit: function (e) {
            var formData = new FormData(e.currentTarget.form);

            formData.append('form_key', $.mage.cookies.get('form_key'));
            formData.append('isAjax', 1);

            $.ajax({
                url: this.options.action,
                data: formData,
                processData: false,
                contentType: false,
                type: 'post',
                dataType: 'json',
                context: this,

                /** @inheritdoc */
                beforeSend: function () {
                    $('body').trigger('processStart');
                },

                /** @inheritdoc */
                success: function (data) {
                    this.displayMessages(data.messages, false);
                    $(e.currentTarget.form).find('textarea').val('');
                },

                /** @inheritdoc */
                error: function () {
                    this.hideChat();
                },

                /** @inheritdoc */
                complete: function () {
                    $('body').trigger('processStop');
                }
            });
        },

        /**
         * Displaying messages into the chat.
         * @param {Array} messages
         * @param {Boolean} isAdmin
         */
        displayMessages: function (messages, isAdmin) {
            var messageList = $(this.options.chatContainerSelector).find('.messages');

            this.chatContainer.trigger('mykhailok_SupportChat_chat-messages-added.namespace_mykhailok_SupportChat', {
                'messages': messages,
                'isAdmin': isAdmin
            });
            messageList.scrollTop(messageList[0].scrollHeight);
        }
    });

    return $.mykhailokSupportChat.chat;
});
