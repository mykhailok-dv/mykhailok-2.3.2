define([
    'jquery',
    'mage/mage',
    'Magento_Ui/js/modal/modal'
], function ($) {
    'use strict';

    $.widget('mykhailokSupportChat.chat', {
        options: {
            openLinkButtonSelector: '.mykhailok-support-chat-link.open-chat',
            closeChatButtonSelector: '#mykhailok-support-chat-close-button',
            submitMessageButtonSelector: '#mykhailok-support-chat-submit-button',
            form: '#mykhailok-support-chat',
            action: ''
        },

        /**
         * @private
         */
        _create: function () {
            this.modal = $(this.element).modal({
                buttons: []
            });
            $(document).on('mykhailok_SupportChat_showChat.namespace_mykhailok_SupportChat', $.proxy(this.showChat, this));
            $(this.options.closeChatButtonSelector).on('click.namespace_mykhailok_SupportChat', $.proxy(this.hideChat, this));
            $(this.options.submitMessageButtonSelector).on('click.namespace_mykhailok_SupportChat', $.proxy(this.submitMessage, this));
        },

        /**
         * @private
         */
        _destroy: function () {
            this.modal.closeModal();
            $(document).off('mykhailok_SupportChat_showChat.namespace_mykhailok_SupportChat');
            $(this.options.closeChatButtonSelector).off('click.namespace_mykhailok_SupportChat');
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
            //$(this.element).removeClass('active');
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
                            'message': formData.get('message'),
                            'time': (+new Date()) / 1000
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
         *
         * @param messages
         * @param isAdmin
         */
        displayMessages: function (messages, isAdmin = false) {
            var timeConverter;
            timeConverter = function (timestamp) {
                var a = new Date(timestamp * 1000),
                    months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                return `${a.getDate()} ${months[a.getMonth()]} ${a.getFullYear()} ${a.getHours()}:${a.getMinutes()}:${a.getSeconds()}`;
            };

            messages.forEach(function (item) {
                var messageList = $('#mykhailok-support-chat .messages');
                var element = $($('#mykhailok-support-chat').find('.messages').html());

                if (isAdmin) {
                    element.removeClass('left').addClass('right');
                }

                var title = (isAdmin ? 'Admin' : 'You') + ' | ' + timeConverter(item.time);
                element.find('.item-time').html(title);
                element.find('.item-message').html(item.message);
                messageList.append(element[0]);
                messageList.scrollTop(messageList[0].scrollHeight);
            });
        }
    });

    return $.mykhailokSupportChat.chat;
});
