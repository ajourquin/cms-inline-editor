<?php

/**
 * @author Aurélien Jourquin <aurelien@growzup.com>
 * @link https://www.growzup.com
 */

declare(strict_types=1);

namespace Ajourquin\CmsInlineEditor\Model\Wysiwyg;

use Magento\Backend\Model\UrlInterface;
use Magento\Cms\Model\Wysiwyg\CompositeConfigProvider;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Wysiwyg\ConfigInterface;

class Config extends DataObject implements ConfigInterface
{
    private const WYSIWYG_SKIN_IMAGE_PLACEHOLDER_ID = 'Ajourquin_CmsInlineEditor::images/wysiwyg_skin_image.png';

    /** @var Repository */
    private $assetRepo;

    /** @var array */
    private $windowSize;

    /** @var UrlInterface */
    private $backendUrl;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var Filesystem */
    private $filesystem;

    /** @var CompositeConfigProvider|null */
    private $configProvider;

    /**
     * Config constructor.
     * @param UrlInterface $backendUrl
     * @param Repository $assetRepo
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param CompositeConfigProvider $compositeConfigProvider
     * @param array $windowSize
     * @param array $data
     * @param CompositeConfigProvider|null $configProvider
     */
    public function __construct(
        UrlInterface $backendUrl,
        Repository $assetRepo,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        CompositeConfigProvider $compositeConfigProvider,
        array $windowSize = [],
        array $data = [],
        ?CompositeConfigProvider $configProvider = null
    ) {
        parent::__construct($data);

        $this->backendUrl = $backendUrl;
        $this->assetRepo = $assetRepo;
        $this->windowSize = $windowSize;
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->configProvider = $configProvider ?: $compositeConfigProvider;
    }

    /**
     * @param array|DataObject $data
     * @return DataObject
     */
    public function getConfig($data = []): DataObject
    {
        $config = new DataObject();

        $config->setData(
            [
                'baseStaticUrl' => $this->assetRepo->getStaticViewFileContext()->getBaseUrl(),
                'baseStaticDefaultUrl' => \str_replace('index.php/', '', $this->backendUrl->getBaseUrl())
                    . $this->filesystem->getUri(DirectoryList::STATIC_VIEW) . '/',
                'directives_url' => $this->backendUrl->getUrl('cms/wysiwyg/directive'),
                'add_variables' => true,
                'add_widgets' => true,
                'add_directives' => true,
                'width' => '100%',
                'height' => '600px',
                'plugins' => []
            ]
        );

        $config->setData('directives_url_quoted', \preg_quote($config->getData('directives_url')));

        if (\is_array($data)) {
            $config->addData($data);
        }

        $this->configProvider->processGalleryConfig($config);
        $config->addData(
            [
                'files_browser_window_width' => $this->windowSize['width'],
                'files_browser_window_height' => $this->windowSize['height'],
            ]
        );

        if ($config->getData('add_widgets')) {
            $this->configProvider->processWidgetConfig($config);
        }

        if ($config->getData('add_variables')) {
            $this->configProvider->processVariableConfig($config);
        }

        return $this->configProvider->processWysiwygConfig($config);
    }

    /**
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getSkinImagePlaceholderPath(): string
    {
        $staticPath = $this->storeManager->getStore()->getBaseStaticDir();
        $placeholderPath = $this->assetRepo->createAsset(self::WYSIWYG_SKIN_IMAGE_PLACEHOLDER_ID)->getPath();

        return $staticPath . '/' . $placeholderPath;
    }
}
