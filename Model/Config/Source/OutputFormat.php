<?php

/**
 * Aceextension Image Optimizer
 *
 * @category   Aceextension
 * @package    Aceextension_ImageOptmizer
 * @author     Aceextension
 */

declare(strict_types=1);

namespace Aceextension\ImageOptmizer\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class OutputFormat implements OptionSourceInterface
{
    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'webp', 'label' => __('WebP (Recommended)')],
            ['value' => 'avif', 'label' => __('AVIF (Next-Gen Compression)')],
        ];
    }
}
