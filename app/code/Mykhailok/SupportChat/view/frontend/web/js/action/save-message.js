define([
    'jquery'
], function ($) {
    'use strict';

    /**
     * @param {Object} data
     * @param {String} url
     */
    return function (data, url) {
        return $.ajax({
            url: url,
            data: data,
            type: 'post',
            dataType: 'json',
            context: this,

            /** @inheritdoc */
            beforeSend: function () {
                $('body').trigger('processStart');
            },

            /** @inheritdoc */
            complete: function () {
                $('body').trigger('processStop');
            }
        });
    };
});
