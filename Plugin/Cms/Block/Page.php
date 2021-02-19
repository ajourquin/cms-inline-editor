<?php

/**
 * @author Aurélien Jourquin <aurelien@growzup.com>
 * @link https://www.growzup.com
 */

declare(strict_types=1);

namespace Ajourquin\CmsInlineEditor\Plugin\Cms\Block;

use Ajourquin\CmsInlineEditor\Helper\Config;
use Magento\Cms\Block\Page as MagentoPage;
use Magento\Framework\App\RequestInterface;

class Page
{
    /** @var Config */
    private $config;

    /** @var RequestInterface */
    private $request;

    /**
     * Page constructor.
     * @param Config $config
     * @param RequestInterface $request
     */
    public function __construct(
        Config $config,
        RequestInterface $request
    ) {
        $this->config = $config;
        $this->request = $request;
    }

    /**
     * @param MagentoPage $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml(MagentoPage $subject, string $result): string
    {
        if ($this->config->isEnabled()) {
            $pageId = $this->getPageId();

            if ($pageId > 0) {
                $result = '<div id="cie-cms-' . $pageId . '" class="cie-box"
                    data-trigger="cie-trigger" data-cie-cms="' . $pageId . '"
                    data-cie-type="page">'
                    . $result
                    . '</div>';
            }
        }

        return $result;
    }

    /**
     * @return int|null
     */
    private function getPageId(): ?int
    {
        return (int) $this->request->getParam('page_id', $this->request->getParam('id'));
    }
}
