<?php

declare(strict_types=1);

namespace Aceextension\ImageOptmizer\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Aceextension\ImageOptmizer\Helper\Data as ImageHelper;

class ConfigProvider extends Template
{
    /**
     * @param Template\Context $context
     * @param ImageHelper $imageHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        private readonly ImageHelper $imageHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get JSON configuration for frontend Admin JS
     *
     * @return string
     */
    public function getConfigJson(): string
    {
        return json_encode([
            'svgEnabled' => $this->imageHelper->isSvgUploadEnabled()
        ]);
    }
}
