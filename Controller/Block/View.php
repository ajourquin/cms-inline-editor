<?php

/**
 * @author Aurélien Jourquin <aurelien@growzup.com>
 * @link https://www.growzup.com
 */

declare(strict_types=1);

namespace Ajourquin\CmsInlineEditor\Controller\Block;

use Ajourquin\CmsInlineEditor\Model\Editor;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class View extends Action
{
    /** @var BlockRepositoryInterface */
    private $blockRepository;

    /** @var Editor */
    private $editor;

    /**
     * View constructor.
     * @param Context $context
     * @param BlockRepositoryInterface $blockRepository
     */
    public function __construct(
        Context $context,
        BlockRepositoryInterface $blockRepository,
        Editor $editor
    ) {
        parent::__construct($context);

        $this->blockRepository = $blockRepository;
        $this->editor = $editor;
    }

    /**
     * @return ResultInterface
     * @throws LocalizedException
     */
    public function execute(): ResultInterface
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        if ($this->getRequest()->isAjax() && $this->editor->canEdit()) {
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

            $resultJson->setData([
                'id' => $this->getRequest()->getParam('id'),
                'identifier' => $this->getCmsBlock()->getIdentifier(),
                'content' => $this->getCmsBlock()->getContent()
            ]);
        } else {
            $resultJson->setHttpResponseCode(400);
        }

        return $resultJson;
    }

    /**
     * @return BlockInterface
     * @throws LocalizedException
     */
    private function getCmsBlock(): BlockInterface
    {
        $blockId = $this->getRequest()->getParam('id');

        return $this->blockRepository->getById($blockId);
    }
}
