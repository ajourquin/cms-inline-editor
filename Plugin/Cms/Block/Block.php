<?php

/**
 * @author Aurélien Jourquin <aurelien@growzup.com>
 * @link https://www.growzup.com
 */

declare(strict_types=1);

namespace Ajourquin\CmsInlineEditor\Plugin\Cms\Block;

use Ajourquin\CmsInlineEditor\Helper\Config;
use Ajourquin\CmsInlineEditor\Model\ResourceModel\Block as ResourceBlock;
use Magento\Cms\Block\Block as MagentoBlock;
use Magento\Framework\Exception\LocalizedException;

class Block
{
    /** @var ResourceBlock */
    private $resourceBlock;

    /** @var Config */
    private $config;

    /**
     * Block constructor.
     * @param ResourceBlock $resourceBlock
     * @param Config $config
     */
    public function __construct(
        ResourceBlock $resourceBlock,
        Config $config
    ) {
        $this->resourceBlock = $resourceBlock;
        $this->config = $config;
    }

    /**
     * @param MagentoBlock $subject
     * @param string $result
     * @return string
     * @throws LocalizedException
     */
    public function afterToHtml(MagentoBlock $subject, string $result): string
    {
        if ($this->config->isEnabled()) {
            $blockId = $this->getBlockId($subject->getData('block_id'));

            if ($blockId !== null) {
                $result = '<div id="cie-block-' . $blockId . '" class="cie-box"
                    data-trigger="cie-trigger" data-cie-block="' . $blockId . '">'
                    . $result
                    . '</div>';
            }
        }

        return $result;
    }

    /**
     * @param string $identifier
     * @return int|null
     * @throws LocalizedException
     */
    private function getBlockId(string $identifier): ?int
    {
        return $this->resourceBlock->getIdByIdentifier($identifier);
    }
}
