<?php

/**
 * @author Aurélien Jourquin <aurelien@growzup.com>
 * @link https://www.growzup.com
 */

declare(strict_types=1);

namespace Ajourquin\CmsInlineEditor\Model\Wysiwyg;

use Magento\Framework\Data\Wysiwyg\ConfigProviderInterface;
use Magento\Framework\DataObject;
use Magento\Framework\View\Asset\Repository;

class DefaultConfigProvider implements ConfigProviderInterface
{
    /** @var Repository */
    private $assetRepo;

    /**
     * DefaultConfigProvider constructor.
     * @param Repository $assetRepo
     */
    public function __construct(
        Repository $assetRepo
    ) {
        $this->assetRepo = $assetRepo;
    }

    /**
     * @param DataObject $config
     * @return DataObject
     */
    public function getConfig(DataObject $config): DataObject
    {
        $config->addData([
            'tinymce4' => [
                'toolbar' => 'undo redo | bold italic underline strikethrough | '
                    . ' formatselect fontselect fontsizeselect | alignleft aligncenter alignright alignjustify | '
                    . ' outdent indent |  numlist bullist | forecolor backcolor removeformat | charmap emoticons | '
                    . ' image link anchor | code',
                'plugins' => \implode(
                    ' ',
                    [
                        'autosave',
                        'paste',
                        'searchreplace',
                        'autolink',
                        'code',
                        'image',
                        'link',
                        'table',
                        'charmap',
                        'anchor',
                        'advlist',
                        'lists',
                        'wordcount',
                        'help',
                        'emoticons',
                        'textcolor'
                    ]
                ),
                'content_css' => $this->assetRepo->getUrl('Ajourquin_CmsInlineEditor::css/tiny_mce.css'),
            ]
        ]);

        return $config;
    }
}
