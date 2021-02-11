<?php

/**
 * @author Aurélien Jourquin <aurelien@growzup.com>
 * @link https://www.growzup.com
 */

declare(strict_types=1);

namespace Ajourquin\CmsInlineEditor\Controller\Block;

use Ajourquin\CmsInlineEditor\Model\Editor;
use Magento\Cms\Model\BlockRepository;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;

class Post extends Action
{
    /** @var BlockRepository */
    private $blockRepository;

    /** @var FilterProvider */
    private $filterProvider;

    /** @var SerializerInterface */
    private $serializer;

    /** @var Editor */
    private $editor;

    /**
     * Post constructor.
     * @param Context $context
     * @param BlockRepository $blockRepository
     * @param FilterProvider $filterProvider
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Context $context,
        BlockRepository $blockRepository,
        FilterProvider $filterProvider,
        SerializerInterface $serializer,
        Editor $editor
    ) {
        parent::__construct($context);

        $this->blockRepository = $blockRepository;
        $this->filterProvider = $filterProvider;
        $this->serializer = $serializer;
        $this->editor = $editor;
    }

    /**
     * @return ResultInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function execute(): ResultInterface
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        if ($this->getRequest()->isAjax() && $this->editor->canEdit()) {
            $params = $this->getRequest()->getParams();
            $unserializedContent = $this->serializer->unserialize($params['content']);

            $result = [
                'success' => true,
                'id' => (int) $params['id'],
                'content' => $this->filterProvider->getBlockFilter()->filter($unserializedContent)
            ];

            $block = $this->blockRepository->getById($params['id']);
            $block->setContent($unserializedContent);
            $this->blockRepository->save($block);

            $resultJson->setData($result);
        } else {
            $resultJson->setHttpResponseCode(400);
        }

        return $resultJson;
    }
}
