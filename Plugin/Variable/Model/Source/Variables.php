<?php

/**
 * @author Aurélien Jourquin <aurelien@growzup.com>
 * @link https://www.growzup.com
 */

declare(strict_types=1);

namespace Ajourquin\CmsInlineEditor\Plugin\Variable\Model\Source;

use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Variable\Model\Source\Variables as MagentoVariables;

class Variables
{
    /** @var CacheInterface */
    private $cache;

    /** @var SerializerInterface */
    private $serializer;

    /** @var ArrayUtils */
    private $arrayUtils;

    /**
     * Variables constructor.
     * @param CacheInterface $cache
     * @param SerializerInterface $serializer
     * @param ArrayUtils $arrayUtils
     */
    public function __construct(
        CacheInterface $cache,
        SerializerInterface $serializer,
        ArrayUtils $arrayUtils
    ) {
        $this->cache = $cache;
        $this->serializer = $serializer;
        $this->arrayUtils = $arrayUtils;
    }

    /**
     * @param MagentoVariables $subject
     * @param array $result
     * @return array
     */
    public function afterGetData(MagentoVariables $subject, array $result): array
    {
        $data = $this->cache->load('adminhtml::backend_system_configuration_structure');

        if ($data !== false) {
            $data = $this->serializer->unserialize($data);
            $data = $this->arrayUtils->flatten($data['config']['system']['sections']);
            $data = $this->sanitizeKeys($data);

            foreach ($result as &$configVariable) {
                $configVariable['label'] = \__($data[$configVariable['value'] . '/label']);
            }
        } else {
            foreach ($result as &$configVariable) {
                $configVariable['label'] = $configVariable['value'];
            }
        }

        return $result;
    }

    /**
     * @param array $data
     * @return array
     */
    private function sanitizeKeys(array $data): array
    {
        $newData = [];

        foreach ($data as $key => $value) {
            $newData[\str_replace('/children', '', $key)] = $value;
        }

        return $newData;
    }
}
