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

class Init extends Action
{
    /** @var Editor */
    private $editor;

    /**
     * Init constructor.
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
     */
    public function execute(): ResultInterface
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        if ($this->getRequest()->isAjax()) {
            $data = [];
            $data['isAllowed'] = false;
            $canEdit = $this->editor->canEdit();

            if ($canEdit) {
                $data['isAllowed'] = true;
                $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
                $data['content'] = $resultLayout->getLayout()->getOutput();
            }

            $resultJson->setData($data);
        }

        return $resultJson;
    }
}
