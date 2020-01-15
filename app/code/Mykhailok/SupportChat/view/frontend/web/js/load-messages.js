define([
    'jquery',
    'mage/mage',
    'Mykhailok_SupportChat/js/chat',
    'Mykhailok_SupportChat/js/listener/message-listener'
], function ($) {
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
            var formData = new FormData();

            formData.append('form_key', $.mage.cookies.get('form_key'));
            formData.append('isAjax', '1');

            $.ajax({
                url: this.options.action,
                data: formData,
                processData: false,
                contentType: false,
                type: 'post',
                dataType: 'json',
                context: this,

                /** @inheritdoc */
                success: function (data) {
                    if (!data.success) {
                        return;
                    }

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
