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
        $options = [
            ['value' => 'webp', 'label' => __('WebP (Recommended)')],
        ];

        if (function_exists('imageavif') && function_exists('imagecreatefromavif')) {
            $options[] = ['value' => 'avif', 'label' => __('AVIF (Next-Gen Compression)')];
        } else {
            $options[] = ['value' => 'avif', 'label' => __('AVIF (Not Supported by server, will fallback)')];
        }

        return $options;
    }
}
