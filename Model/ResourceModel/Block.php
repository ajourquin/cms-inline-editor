<?php

/**
 * @author Aurélien Jourquin <aurelien@growzup.com>
 * @link https://www.growzup.com
 */

declare(strict_types=1);

namespace Ajourquin\CmsInlineEditor\Model\ResourceModel;

use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Model\ResourceModel\Block as ResourceBlock;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

class Block
{
    /** @var ResourceBlock */
    private $resourceBlock;

    /** @var MetadataPool */
    private $metadataPool;

    /** @var StoreManagerInterface */
    private $storeManager;

    /**
     * Block constructor.
     * @param ResourceBlock $resourceBlock
     * @param MetadataPool $metadataPool
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceBlock $resourceBlock,
        MetadataPool $metadataPool,
        StoreManagerInterface $storeManager
    ) {
        $this->resourceBlock = $resourceBlock;
        $this->metadataPool = $metadataPool;
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $identifier
     * @return int|null
     * @throws LocalizedException
     */
    public function getIdByIdentifier(string $identifier): ?int
    {
        $blockId = null;
        $connection = $this->resourceBlock->getConnection();
        $entityMetadata = $this->metadataPool->getMetadata(BlockInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
                    ->from($this->resourceBlock->getMainTable(), [$linkField])
                    ->join(
                        ['cbs' => $this->resourceBlock->getTable('cms_block_store')],
                        $this->resourceBlock->getMainTable() . '.' . $linkField . ' = cbs.' . $linkField,
                        []
                    )
                    ->where('identifier = ?', $identifier)
                    ->where('cbs.store_id IN (?)', [0, (int) $this->storeManager->getStore()->getId()])
                    ->order('cbs.store_id DESC')
                    ->limit(1);

        $result = $connection->fetchCol($select);

        if (\count($result) > 0) {
            $blockId = (int) $result[0];
        }

        return $blockId;
    }
}
