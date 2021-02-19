<?php

/**
 * @author Aurélien Jourquin <aurelien@growzup.com>
 * @link https://www.growzup.com
 */

declare(strict_types=1);

namespace Ajourquin\CmsInlineEditor\Model;

use Ajourquin\CmsInlineEditor\Helper\Config;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Filter\Template;
use Magento\Framework\Serialize\SerializerInterface;

class Editor
{
    public const TYPE_PAGE = 'page';
    public const TYPE_BLOCK = 'block';

    /** @var BackendSession */
    private $backendSession;

    /** @var Config */
    private $config;

    /** @var BlockRepositoryInterface */
    private $blockRepository;

    /** @var PageRepositoryInterface */
    private $pageRepository;

    /** @var FilterProvider */
    private $filterProvider;

    /** @var SerializerInterface */
    private $serializer;

    /**
     * Editor constructor.
     * @param BackendSession $backendSession
     * @param Config $config
     * @param BlockRepositoryInterface $blockRepository
     * @param PageRepositoryInterface $pageRepository
     * @param FilterProvider $filterProvider
     * @param SerializerInterface $serializer
     */
    public function __construct(
        BackendSession $backendSession,
        Config $config,
        BlockRepositoryInterface $blockRepository,
        PageRepositoryInterface $pageRepository,
        FilterProvider $filterProvider,
        SerializerInterface $serializer
    ) {
        $this->backendSession = $backendSession;
        $this->config = $config;
        $this->blockRepository = $blockRepository;
        $this->pageRepository = $pageRepository;
        $this->filterProvider = $filterProvider;
        $this->serializer = $serializer;
    }

    /**
     * @return bool
     */
    public function canEdit(): bool
    {
        return $this->config->isEnabled()
            && $this->backendSession->getData('cieAllowed') === true;
    }

    /**
     * @param string $context
     * @return BlockRepositoryInterface|PageRepositoryInterface
     */
    public function getContextRepository(string $context)
    {
        return $context === self::TYPE_PAGE
            ? $this->pageRepository
            : $this->blockRepository;
    }

    /**
     * @param string $cmsType
     * @param sting $content
     * @return string
     * @throws \Exception
     */
    public function filter(string $cmsType, string $content): string
    {
        $content = $this->serializer->unserialize($content);

        return $this->getFilter($cmsType)->filter($content);
    }

    /**
     * @param string $type
     * @return Template
     */
    private function getFilter(string $type): Template
    {
        return $type === Editor::TYPE_PAGE
            ? $this->filterProvider->getPageFilter()
            : $this->filterProvider->getBlockFilter();
    }
}
