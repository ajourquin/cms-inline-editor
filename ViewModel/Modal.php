<?php

/**
 * @author Aurélien Jourquin <aurelien@growzup.com>
 * @link https://www.growzup.com
 */

declare(strict_types=1);

namespace Ajourquin\CmsInlineEditor\ViewModel;

use Ajourquin\CmsInlineEditor\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Modal implements ArgumentInterface
{
    /** @var WysiwygConfig */
    private $wysiwygConfig;

    /** @var UrlInterface */
    private $url;

    /** @var FormKey */
    private $formKey;

    /** @var SerializerInterface */
    private $serializer;

    /**
     * Modal constructor.
     * @param UrlInterface $url
     * @param WysiwygConfig $wysiwygConfig
     * @param FormKey $formKey
     * @param SerializerInterface $serializer
     */
    public function __construct(
        UrlInterface $url,
        WysiwygConfig $wysiwygConfig,
        FormKey $formKey,
        SerializerInterface $serializer
    ) {
        $this->url = $url;
        $this->wysiwygConfig = $wysiwygConfig;
        $this->formKey = $formKey;
        $this->serializer = $serializer;
    }

    /**
     * @return string
     */
    public function getPostUrl(): string
    {
        return $this->url->getUrl('cmsinlineeditor/editor/post');
    }

    /**
     * @return string
     */
    public function getInitUrl(): string
    {
        return $this->url->getUrl('cmsinlineeditor/editor/init');
    }

    /**
     * @return string
     */
    public function getViewUrl(): string
    {
        return $this->url->getUrl('cmsinlineeditor/editor/view');
    }

    /**
     * @return string
     */
    public function getWysiwygConfig(): string
    {
        $wysiwygConfig = $this->wysiwygConfig->getConfig()->toArray();

        return $this->serializer->serialize($wysiwygConfig);
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getFormKey(): string
    {
        return $this->formKey->getFormKey();
    }
}
