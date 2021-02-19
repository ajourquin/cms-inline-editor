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
        loadingContent: $t('Loading data. Please wait...'),
        successTitle: $t('Success'),
        successContent: $t('Content has been saved.'),
        errorTitle: $t('Error'),
        errorContent: $t('An error occurred while saving block. Please try again.'),


        /**
         * @param config
         * @param element
         */
        initialize: function (config, element) {
            const self = this;
            this._super();
            const $h2 = $('#cie-block-identifier span');
            const $cmsId = $('#cie-cms-id');
            const $cmsType = $('#cie-cms-type');

            $(element).click(function () {
                const tinyMce = self.getTinyMce();

                if ($(this).data('preview') === 0) {
                    $.ajax({
                        beforeSend: function () {
                            $h2.html(self.loadingContent);
                            tinyMce.setContent(self.loadingContent);
                        },
                        url: config.url,
                    }).success(function (response) {
                        tinyMce.setContent(response.content);
                        $cmsId.val(response.id);
                        $cmsType.val(response.type);
                        $h2.html(response.identifier);
                    }).fail(function (response) {
                        console.log('failed' + response);
                    });
                } else {
                    tinyMce.setContent($('#cie-preview').val());
                    $cmsId.val($(this).data('cie-cms'));
                    $cmsType.val($(this).data('cie-type'));
                }
            });
        },

        /**
         * @param cieModal
         */
        save: function (cieModal) {
            const self = this;
            const $elem = self.getCurrentCmsElem();
            const $cmsId = $('#cie-cms-id').val();
            const $cmsType = $('#cie-cms-type').val();
            const tinyMce = self.getTinyMce();

            $.post({
                url: self.postUrl,
                data: {
                    content: JSON.stringify(tinyMce.getContent()),
                    id: $cmsId,
                    type: $cmsType
                }
            }).success(function (response) {
                $elem.html(response['content']);
                $elem.data('preview', 0);
                self.setResultModalContent(self.successTitle, self.successContent);
            }).fail(function (response) {
                self.setResultModalContent(self.errorTitle, self.errorContent);
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
            const self = this;
            const $cmsElem = self.getCurrentCmsElem();
            const tinyMce = self.getTinyMce();

            self.resetPreview();
            $cmsElem.html(tinyMce.getContent());
            $('#cie-preview').val(tinyMce.getContent());
            $cmsElem.data('preview', 1);
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
            const self = this;

            self.resultModalOptions.title = $t(title);
            self.$resultModalElem.html($t(content));
        },

        /**
         */
        createEditorModal: function () {
            const self = this;

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
                {
                    text: $t('Preview'),
                    class: 'action-secondary action-default',
                    click: function () {
                        self.preview(this);
                    }
                },
                {
                    text: $t('Save'),
                    class: 'action-primary action-default',
                    click: function () {
                        self.save(this);
                    }
                }]
            });
        },

        /**
         * @returns {*|jQuery|HTMLElement}
         */
        getCurrentCmsElem: function () {
            const $cmsId = $('#cie-cms-id').val();

            return $('#cie-cms-' + $cmsId);
        },

        /**
         */
        resetPreview: function () {
            $('.cie-box').data('preview', 0);
        },

        /**
         * @returns {tinymce.Editor}
         */
        getTinyMce: function () {
            return tinymce.get('cie-textarea');
        }
    });
});
