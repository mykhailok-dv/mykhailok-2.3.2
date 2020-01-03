define([
    "jquery",
    "mage/mage"
], function ($) {
    "use strict";

    $.widget("mykhailokSupportChat.chat", {
        options: {
            "openLinkButtonSelector": ".mykhailok-support-chat-link.open-chat",
            "closeChatButtonSelector": "#mykhailok-support-chat-close-button",
            "submitMessageButtonSelector": "#mykhailok-support-chat-submit-button",
            action: ''
        },

        /**
         * @private
         */
        "_create": function () {
            // eslint-disable-next-line max-len
            $(document).on("mykhailok_SupportChat_showChat.namespace_mykhailok_SupportChat", $.proxy(this.showChat, this));
            // eslint-disable-next-line max-len
            $(this.options.closeChatButtonSelector).on("click.namespace_mykhailok_SupportChat", $.proxy(this.hideChat, this));
            // eslint-disable-next-line max-len
            $(this.options.submitMessageButtonSelector).on("click.namespace_mykhailok_SupportChat", $.proxy(this.submitMessage, this));
        },

        /**
         * @private
         */
        "_destroy": function () {
            $(document).off("mykhailok_SupportChat_showChat");
            $(this.options.closeChatButtonSelector).off("click.namespace_mykhailok_SupportChat");
            $(this.options.submitMessageButtonSelector).off("click.namespace_mykhailok_SupportChat");
        },

        /**
         * Shows the chat block.
         */
        showChat: function () {
            $(this.element).addClass("active");
        },

        /**
         * Hides the chat block.
         * @see openChatButton.openLinkButton()
         */
        hideChat: function () {
            $(this.element).removeClass("active");
            $(this.options.openLinkButtonSelector).trigger("mykhailok_SupportChat_hideChat");
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
            var s = $(this.options.submitMessageButtonSelector).parents('form:first');
            return s.valid();
        },

        /**
         * Submit request via AJAX. Add form key to the post data.
         */
        ajaxSubmit: function () {
            var form = $(this.options.submitMessageButtonSelector).parents('form:first');
            var formData = new FormData(form[0]);

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
                // eslint-disable-next-line no-shadow
                success: function (data) {
                    $('body').trigger('processStop');
                    this.displayMessages([{"message": formData.get('message'), 'time': (+new Date()) / 1000}], 'right');
                    if (data.success) {
                        this.displayMessages(data.messages);
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
         * @param position
         */
        displayMessages: function (messages, position) {
            let timeConverter;
            timeConverter = function (UNIX_timestamp) {
                var a = new Date(UNIX_timestamp * 1000);
                var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                return `${a.getDate()} ${months[a.getMonth()]} ${a.getFullYear()} ${a.getHours()}:${a.getMinutes()}:${a.getSeconds()}`;
            };

            position = position || 'left';
            messages.forEach(function (item) {
                var messageList = $('#mykhailok-support-chat .messages');
                var element = $($('#mykhailok-support-chat').find('.messages').html());
                if (position === 'right') {
                    element.removeClass('left').addClass('right');
                }

                var title = (position === 'right' ? '  You' : 'Admin') + ' | ' + timeConverter(item.time);
                element.find('.item-time').html(title);
                element.find('.item-message').html(item.message);
                messageList.append(element[0]);
                messageList.scrollTop(messageList[0].scrollHeight);
            });
        }
    });

    return $.mykhailokSupportChat.chat;
});