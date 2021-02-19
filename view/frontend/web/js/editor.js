define([
    'uiClass',
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
], function (Class, $, modal, $t) {
    'use strict';

    return Class.extend({
        postUrl: null,
        resultModalInitialized: null,
        $resultModalElem: $('#cie-result-modal'),
        resultModalOptions: {
            autoOpen: false,
            modalClass: 'cie-result-modal',
            type: 'popup',
            responsive: true,
            innerScroll: false,
            clickableOverlay: true,
            buttons: [{
                text: $t('Close'),
                class: 'action-primary'
            }]
        },

        /**
         * @param config
         * @param element
         */
        initialize: function (config, element) {
            var self = this;
            this._super();

            $(element).click(function () {
                $.ajax({
                    beforeSend: function () {
                        $('#cie-block-identifier span').html($t('Loading data. Please wait...'));
                        tinymce.get('cie-textarea').setContent($t('Loading data. Please wait...'));
                    },
                    url: config.url,
                }).success(function (response) {
                    tinymce.get('cie-textarea').setContent(response.content);
                    $('#cie-cms-id').val(response.id);
                    $('#cie-cms-type').val(response.type);
                    $('#cie-block-identifier span').html(response.identifier);
                }).fail(function (response) {
                    console.log('failed' + response);
                });
            });
        },

        /**
         * @param cieModal
         */
        save: function (cieModal) {
            var self = this;
            const $cmsId = $('#cie-cms-id').val();
            const $cmsType = $('#cie-cms-type').val();

            $.post({
                url: self.postUrl,
                data: {
                    content: JSON.stringify(tinymce.get('cie-textarea').getContent()),
                    id: $cmsId,
                    type: $cmsType
                }
            }).success(function (response) {
                $('#cie-cms-' + $cmsId).html(response['content']);
                self.setResultModalContent('Success', 'Content has been saved.');
            }).fail(function (response) {
                self.setResultModalContent('Error', 'An error occurred while saving block. Please try again.');
            }).complete(function () {
                if (self.resultModalInitialized === null) {
                    self.resultModalInitialized = modal(
                        self.resultModalOptions,
                        self.$resultModalElem
                    );
                } else {
                    self.resultModalInitialized.setTitle(self.resultModalOptions.title);
                }

                self.$resultModalElem.trigger('openModal');
                cieModal.closeModal();
            });
        },

        /**
         * @param cieModal
         */
        preview: function (cieModal) {
            const $cmsId = $('#cie-cms-id').val();

            $('#cie-cms-' + $cmsId).html(tinymce.get('cie-textarea').getContent());
            cieModal.closeModal();
        },

        /**
         * @param url
         */
        setPostUrl: function (url) {
            this.postUrl = url;
        },

        /**
         * @param title
         * @param content
         */
        setResultModalContent: function (title, content) {
            var self = this;

            self.resultModalOptions.title = $t(title);
            self.$resultModalElem.html($t(content));
        },

        /**
         */
        createEditorModal: function () {
            var self = this;

            $('#cie-modal').modal({
                type: 'slide',
                title: 'Cms Inline Editor',
                modalClass: 'cie-modal-wrapper',
                trigger: '[data-trigger=cie-trigger]',
                responsive: true,
                clickableOverlay: true,
                buttons: [{
                    text: $t('Close'),
                    class: 'action-default reset'
                },
                // {
                //     text: $t('Preview'),
                //     class: 'action-secondary action-default',
                //     click: function () {
                //         self.preview(this);
                //     }
                // },
                {
                    text: $t('Save'),
                    class: 'action-primary action-default',
                    click: function () {
                        self.save(this);
                    }
                }]
            });
        }
    });
});
