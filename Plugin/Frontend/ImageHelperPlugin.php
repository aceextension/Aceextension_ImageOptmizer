<?php

declare(strict_types=1);

namespace Aceextension\ImageOptmizer\Plugin\Frontend;

use Magento\Catalog\Helper\Image;
use Aceextension\ImageOptmizer\Helper\Data as ImageHelper;

class ImageHelperPlugin
{
    /**
     * @param ImageHelper $imageHelper
     */
    public function __construct(
        private readonly ImageHelper $imageHelper
    ) {}

    /**
     * Replace JPG/PNG with WebP in generated URLs
     *
     * @param Image $subject
     * @param string $result
     * @return string
     */
    public function afterGetUrl(Image $subject, string $result): string
    {
        return $this->imageHelper->rewriteImageUrl($result);
    }
}
