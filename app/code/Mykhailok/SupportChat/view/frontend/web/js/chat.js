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
                title: $(this.options.form).find('h3').remove().html()
            });
            $(document)
                .on('mykhailok_SupportChat_showChat.namespace_mykhailok_SupportChat', $.proxy(this.showChat, this));
            $(this.options.closeChatButtonSelector)
                .on('click.namespace_mykhailok_SupportChat', $.proxy(this.hideChat, this));
            $(this.options.form)
                .on('modalclosed.namespace_mykhailok_SupportChat', $.proxy(this.hideChat, this));
            $(this.options.submitMessageButtonSelector)
                .on('click.namespace_mykhailok_SupportChat', $.proxy(this.submitMessage, this));
            this.chatContainer = $(this.options.chatContainerSelector);
        },

        /**
         * @private
         */
        _destroy: function () {
            $(document).off('mykhailok_SupportChat_showChat.namespace_mykhailok_SupportChat');
            $(this.options.closeChatButtonSelector).off('click.namespace_mykhailok_SupportChat');
            $(this.options.form).off('modalclosed.namespace_mykhailok_SupportChat');
            $(this.options.submitMessageButtonSelector).off('click.namespace_mykhailok_SupportChat');
        },

        /**
         * Shows the chat block.
         */
        showChat: function () {
            $(this.element).addClass('active');
            $(this.options.form).data('mage-modal').openModal();
        },

        /**
         * Hides the chat block.
         * @see openChatButton.openLinkButton()
         */
        hideChat: function () {
            if ($(this.element).hasClass('active')) {
                $(this.element).removeClass('active');
                $(this.options.form).data('mage-modal').closeModal();
            }
            $(this.options.openLinkButtonSelector).trigger('mykhailok_SupportChat_hideChat');
        },

        /**
         * Submit form action.
         */
        submitMessage: function () {
            if (!this.validateForm()) {
                return;
            }

            this.ajaxSubmit();
        },

        /**
         * Validate request form
         */
        validateForm: function () {
            return $(this.options.submitMessageButtonSelector).parents('form:first').valid();
        },

        /**
         * Submit request via AJAX. Add form key to the post data.
         */
        ajaxSubmit: function () {
            var form = $(this.options.submitMessageButtonSelector).parents('form:first'),
                formData = new FormData(form[0]);

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
                    $('body').trigger('processStop');
                    this.displayMessages([
                        {
                            'text': formData.get('message'),
                            'time': +new Date() / 1000
                        }
                    ], true);

                    if (data.success) {
                        this.displayMessages(data.messages, false);
                    }
                },

                /** @inheritdoc */
                error: function () {
                    $('body').trigger('processStop');
                }
            }).success(function () {
                form.find('textarea').val('');
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
