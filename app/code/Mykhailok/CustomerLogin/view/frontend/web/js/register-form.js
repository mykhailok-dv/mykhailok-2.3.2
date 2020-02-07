define([
        'jquery',
        'uiComponent',
        'Magento_Ui/js/modal/modal'
    ], function ($, Component) {
        'use strict';

        return Component.extend({
            self: this,
            modalOptions: {
                modalClass: 'form-create-account',
                buttons: []
            },
            buttonSelector: '.form-create-account-button',

            /**
             * @inheritdoc
             */
            initialize: function () {
                this._super();
                this.initModal();
                $(this.buttonSelector).on('click', $.proxy(this.showForm, this));
                $(document).on('modalclosed.namespace_mykhailok_SupportChat', $.proxy(this.hideForm, this));
            },

            /**
             * Init modal from the component HTML. Will be exec after render.
             */
            initModal: function (formElement) {
                this.parentForm = $(formElement || '.' + this.modalOptions.modalClass);
                this.formWrapperClass = this.modalOptions.modalClass + '-wrapper';
                this.parentForm.parent().addClass(this.formWrapperClass);
            },

            /**
             * Shows create account form.
             */
            showForm: function () {
                if (typeof this.modal === 'undefined') {
                    this.modal = this.parentForm.modal(this.modalOptions);
                }

                if (typeof this.modalWrapperClass === 'undefined') {
                    this.modalWrapperClass = '#' + this.modal.parent().attr('id');
                }

                this.modal.modal('openModal');
                $(this.modalWrapperClass).append($(this.modal));
            },

            /**
             * Hides create account form.
             */
            hideForm: function () {
                $('.' + this.formWrapperClass).append($(this.modal));
            }
        });
    }
);
