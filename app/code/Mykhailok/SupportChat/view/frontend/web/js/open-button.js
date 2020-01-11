define([
    'jquery',
    'mage/translate'
], function ($) {
    'use strict';

    /**
     * @property mykhailokSupportChat.openChatButton
     */
    $.widget('mykhailokSupportChat.openChatButton', {
        options: {
            hideButton: true
        },

        /**
         * @private
         */
        _create: function () {
            $(this.element).on('click.namespace_mykhailok_SupportChat', $.proxy(this.openChat, this));
            $(this.element).on(
                'mykhailok_SupportChat_hideChat.namespace_mykhailok_SupportChat',
                $.proxy(this.openLinkButton, this)
            );
        },

        /**
         * @private
         */
        _destroy: function () {
            $(this.element).off('click.namespace_mykhailok_SupportChat');
            $(this.element).off('mykhailok_SupportChat_hideChat.namespace_mykhailok_SupportChat');
        },

        /**
         * Open a chat block and hides the link.
         * @see chat.showChat
         */
        openChat: function () {
            $(document).trigger('mykhailok_SupportChat_showChat');

            if (this.options.hideButton) {
                $(this.element).removeClass('active');
            }
        },

        /**
         * Shows the link.
         */
        openLinkButton: function () {
            $(this.element).addClass('active');
        }
    });

    return $.mykhailokSupportChat.openChatButton;
});
