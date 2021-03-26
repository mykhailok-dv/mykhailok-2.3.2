define([
    'jquery',
    'ko',
    'uiComponent',
    'mykhailokQuestionActionSave',
    'mage/cookies'
], function ($, ko, Component, saveMessage) {
    'use strict';

    return Component.extend({
        qNameValue: ko.observable(),
        qEmailValue: ko.observable(),
        qMessageValue: ko.observable(),

        defaults: {
            isVisible: true,
            action: '',
            gProductId: 0,
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();
        },

        initObservable: function () {
            return this._super()
                .observe([
                    'isVisible',
                ])
        },

        /**
         * Form has bind data-bind="click".
         */
        saveQuestion: function () {
            var self = this,
                data = {
                    'name': this.qNameValue(),
                    'email': this.qEmailValue(),
                    'message': this.qMessageValue(),
                    'product_id': this.qProductId,
                    'form_key': $.mage.cookies.get('form_key')
                };

            saveMessage(data, this.action, this);
        },

        toggleForm: function () {
            this.isVisible(!this.isVisible())
        }
    });
});
