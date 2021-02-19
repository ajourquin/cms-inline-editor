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

class Post extends Action
{
    /** @var Editor */
    private $editor;

    /**
     * Post constructor.
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
     * @throws \Exception
     */
    public function execute(): ResultInterface
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        if ($this->getRequest()->isAjax() && $this->editor->canEdit()) {
            $params = $this->getRequest()->getParams();
            $cmsType = $params['type'];
            $repository = $this->editor->getContextRepository($cmsType);
            $content = $this->editor->filter($cmsType, $params['content']);

            $result = [
                'success' => true,
                'id' => (int) $params['id'],
                'content' => $content
            ];

            $entity = $repository->getById($params['id']);
            $entity->setContent($content);
            $repository->save($entity);

            $resultJson->setData($result);
        } else {
            $resultJson->setHttpResponseCode(400);
        }

        return $resultJson;
    }
}
