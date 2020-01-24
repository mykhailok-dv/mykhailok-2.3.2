define([
    'jquery',
    'Mykhailok_SupportChat/js/chat',
    'mage/mage',
    'Mykhailok_SupportChat/js/listener/message-listener'
], function ($, chat) {
    'use strict';

    /**
     * @property mykhailokSupportChat.loadMessages
     */
    $.widget('mykhailokSupportChat.loadMessages', {
        options: {
            action: ''
        },

        /**
         * @private
         */
        _create: function () {
            $(document).on('load', $.proxy(this.loadMessage(), this));
        },

        /**
         * Submit action.
         */
        loadMessage: function () {
            this.ajaxSubmit();
        },

        /**
         * Submit request via AJAX. Add form key to the post data.
         */
        ajaxSubmit: function () {
            $.ajax({
                url: this.options.action,
                contentType: false,
                dataType: 'json',
                context: this,

                /** @inheritdoc */
                success: function (data) {
                    $(this.element).trigger('mykhailok_SupportChat_chat-messages-added.namespace_mykhailok_SupportChat', {
                        'messages': data.messages,
                        'isAdmin': false
                    });
                }
            });
        }
    });

    return $.mykhailokSupportChat.loadMessages;
});
