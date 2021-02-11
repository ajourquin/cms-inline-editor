<?php

/**
 * @author Aurélien Jourquin <aurelien@growzup.com>
 * @link https://www.growzup.com
 */

declare(strict_types=1);

namespace Ajourquin\CmsInlineEditor\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    private const CONFIG_PATH_ENABLED = 'cmsinlineeditor/general/enabled';
    private const CONFIG_PATH_ENABLE_RESTRICTION = 'cmsinlineeditor/general/enable_restriction';
    private const CONFIG_PATH_ALLOWED_IPS = 'cmsinlineeditor/general/allowed_ips';
    private const CONFIG_PATH_VARIABLES_ENABLED = 'cmsinlineeditor/wysiwyg_options/variables_enabled';
    private const CONFIG_PATH_WIDGETS_ENABLED = 'cmsinlineeditor/wysiwyg_options/widgets_enabled';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $xmlPath
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_ENABLED);
    }

    /**
     * @return bool
     */
    public function isRestrictionEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_ENABLE_RESTRICTION);
    }

    /**
     * @return bool
     */
    public function isVariablesEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_VARIABLES_ENABLED);
    }

    /**
     * @return bool
     */
    public function isWidgetsEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_WIDGETS_ENABLED);
    }

    /**
     * @return bool
     */
    public function getAllowedIps(): bool
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_ALLOWED_IPS);
    }
}
