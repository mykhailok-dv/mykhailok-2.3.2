define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('mykhailokSupportChat.toggleChat', {
        options: {
            widget: '.mykhailok-support-chat-link.open-chat'
        },

        /**
         * @private
         */
        _create: function () {
            $(this.element).on('click.namespace_mykhailok_SupportChat', $.proxy(this.toggleChat, this));
        },

        /**
         * @private
         */
        _destroy: function () {
            $(this.element).off('click.namespace_mykhailok_SupportChat');
        },

        /**
         * Disable or enable OpenChatButton widget.
         * @see mykhailokSupportChat.openChatButton
         */
        toggleChat: function () {
            $(this.element).toggleClass('enable');

            if ($(this.element).hasClass('enable')) {
                $($(this.options.widget).get(0)).data('mykhailokSupportChatOpenChatButton').destroy();
                $(this.element).html('-');
            } else {
                $(this.options.widget).openChatButton();
                $(this.element).html('+');
            }
        }
    });

    return $.mykhailokSupportChat.toggleChat;
});
