<?php

declare(strict_types=1);

namespace Aceextension\ImageOptmizer\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Diagnostics extends Field
{
    /**
     * Remove scope label
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $webpSupport = function_exists('imagewebp') && function_exists('imagecreatefromwebp') ?
            '<span style="color: green; font-weight: bold;">Supported</span>' :
            '<span style="color: red; font-weight: bold;">Not Supported</span>';

        $avifSupport = function_exists('imageavif') && function_exists('imagecreatefromavif') ?
            '<span style="color: green; font-weight: bold;">Supported</span>' :
            '<span style="color: red; font-weight: bold;">Not Supported (Requires PHP-GD with AVIF)</span>';

        $html = '<div style="padding: 10px; background-color: #f8f8f8; border: 1px solid #ddd; margin-bottom: 10px;">';
        $html .= '<h4 style="margin-top: 0; margin-bottom: 10px;">Server Environment Diagnostics</h4>';
        $html .= '<ul style="margin: 0; padding-left: 20px;">';
        $html .= '<li style="margin-bottom: 5px;"><strong>WebP Format:</strong> ' . $webpSupport . '</li>';
        $html .= '<li><strong>AVIF Format:</strong> ' . $avifSupport . '</li>';
        $html .= '</ul>';
        $html .= '</div>';

        return $html;
    }
}
