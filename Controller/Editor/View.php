<?php

/**
 * @author Aurélien Jourquin <aurelien@growzup.com>
 * @link https://www.growzup.com
 */

declare(strict_types=1);

namespace Ajourquin\CmsInlineEditor\Controller\Editor;

use Ajourquin\CmsInlineEditor\Model\Editor;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class View extends Action
{
    /** @var Editor */
    private $editor;

    /**
     * View constructor.
     * @param Context $context
     * @param Editor $editor
     */
    public function __construct(
        Context $context,
        Editor $editor
    ) {
        parent::__construct($context);

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
            $cmsType = $this->getRequest()->getParam('type');
            $repository = $this->editor->getContextRepository($cmsType);
            $entity = $repository->getById($this->getRequest()->getParam('id'));

            $resultJson->setData([
                'id' => $this->getRequest()->getParam('id'),
                'type' => $cmsType,
                'identifier' => $entity->getIdentifier(),
                'content' => $entity->getContent()
            ]);
        } else {
            $resultJson->setHttpResponseCode(400);
        }

        return $resultJson;
    }
}
