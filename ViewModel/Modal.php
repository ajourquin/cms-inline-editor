<?php

/**
 * @author Aurélien Jourquin <aurelien@growzup.com>
 * @link https://www.growzup.com
 */

declare(strict_types=1);

namespace Ajourquin\CmsInlineEditor\ViewModel;

use Ajourquin\CmsInlineEditor\Helper\Config;
use Ajourquin\CmsInlineEditor\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Cms\Model\BlockRepository;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Modal implements ArgumentInterface
{
    /** @var RequestInterface */
    private $request;

    /** @var BlockRepository */
    private $blockRepository;

    /** @var WysiwygConfig */
    private $wysiwygConfig;

    /** @var UrlInterface */
    private $url;

    /** @var FormKey */
    private $formKey;

    /** @var SerializerInterface */
    private $serializer;

    /** @var Config */
    private $config;

    /**
     * Modal constructor.
     * @param RequestInterface $request
     * @param BlockRepository $blockRepository
     * @param UrlInterface $url
     * @param WysiwygConfig $wysiwygConfig
     * @param FormKey $formKey
     * @param SerializerInterface $serializer
     * @param Config $config
     */
    public function __construct(
        RequestInterface $request,
        BlockRepository $blockRepository,
        UrlInterface $url,
        WysiwygConfig $wysiwygConfig,
        FormKey $formKey,
        SerializerInterface $serializer,
        Config $config
    ) {
        $this->request = $request;
        $this->blockRepository = $blockRepository;
        $this->url = $url;
        $this->wysiwygConfig = $wysiwygConfig;
        $this->formKey = $formKey;
        $this->serializer = $serializer;
        $this->config = $config;
    }

    /**
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getContent(): string
    {
        $blockId = $this->request->getParam('id');
        $block = $this->blockRepository->getById($blockId);

        return $block->getContent();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return (int) $this->request->getParam('id');
    }

    /**
     * @return string
     */
    public function getPostUrl(): string
    {
        return $this->url->getUrl('cmsinlineeditor/block/post');
    }

    /**
     * @return string
     */
    public function getInitUrl(): string
    {
        return $this->url->getUrl('cmsinlineeditor/block/init');
    }

    /**
     * @return string
     */
    public function getViewUrl(): string
    {
        return $this->url->getUrl('cmsinlineeditor/block/view');
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

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isEnabled();
    }
}
