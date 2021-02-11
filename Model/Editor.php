<?php

/**
 * @author Aurélien Jourquin <aurelien@growzup.com>
 * @link https://www.growzup.com
 */

declare(strict_types=1);

namespace Ajourquin\CmsInlineEditor\Model;

use Ajourquin\CmsInlineEditor\Helper\Config;
use Magento\Backend\Model\Session as BackendSession;

class Editor
{
    /** @var BackendSession */
    private $backendSession;

    /** @var Config */
    private $config;

    /**
     * Editor constructor.
     * @param BackendSession $backendSession
     * @param Config $config
     */
    public function __construct(
        BackendSession $backendSession,
        Config $config
    ) {
        $this->backendSession = $backendSession;
        $this->config = $config;
    }

    /**
     * @return bool
     */
    public function canEdit(): bool
    {
        return $this->config->isEnabled()
            && $this->backendSession->getData('cieAllowed') === true;
    }
}
