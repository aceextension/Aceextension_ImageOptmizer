<?php

declare(strict_types=1);

/**
 * Aceextension_ImageOptmizer
 *
 * @category    Aceextension
 * @package     Aceextension_ImageOptmizer
 * @author      Aceextension
 */

namespace Aceextension\ImageOptmizer\Plugin\App;

use Magento\MediaStorage\App\Media;
use Aceextension\ImageOptmizer\Helper\Data as ImageHelper;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Response\Http as HttpResponse;

class MediaPlugin
{
    /**
     * @param ImageHelper $imageHelper
     */
    public function __construct(
        private readonly ImageHelper $imageHelper
    ) {}

    /**
     * Intercept Media application launch to handle WebP generation on the fly
     *
     * @param Media $subject
     * @param \Closure $proceed
     * @return ResponseInterface
     */
    public function aroundLaunch(Media $subject, \Closure $proceed): ResponseInterface
    {
        if (!$this->imageHelper->isEnabled()) {
            return $proceed();
        }

        if (!$this->imageHelper->isWebpReplacementEnabled()) {
            return $proceed();
        }

        try {
            $reflection = new \ReflectionClass(\Magento\MediaStorage\App\Media::class);

            $relativeFileNameProp = $reflection->getProperty('relativeFileName');
            $relativeFileNameProp->setAccessible(true);
            $requestedFile = $relativeFileNameProp->getValue($subject);

            $format = $this->imageHelper->getOutputFormat();

            if ($requestedFile && preg_match('/\.(' . $format . ')$/i', (string)$requestedFile)) {
                $directoryPubProp = $reflection->getProperty('directoryPub');
                $directoryPubProp->setAccessible(true);
                $directoryPub = $directoryPubProp->getValue($subject);

                $originalModernPath = preg_replace('#/cache/[a-z0-9]+/#i', '/', (string)$requestedFile);

                if ($directoryPub->isFile($originalModernPath)) {
                    // Source is natively WebP/AVIF, let Magento generate cache directly
                    return $proceed();
                }

                $originalPngPath = preg_replace('/\.(' . $format . ')$/i', '.png', $originalModernPath);
                if ($directoryPub->isFile($originalPngPath)) {
                    $sourceFile = preg_replace('/\.(' . $format . ')$/i', '.png', (string)$requestedFile);
                } else {
                    // Fallback to JPG
                    $sourceFile = preg_replace('/\.(' . $format . ')$/i', '.jpg', (string)$requestedFile);
                }

                // Swap the relativeFileName to source file to let Magento generate the cache intermediate first
                $relativeFileNameProp->setValue($subject, $sourceFile);

                /** @var ResponseInterface $response */
                $response = $proceed();

                $sourceAbsolutePath = $directoryPub->getAbsolutePath($sourceFile);
                $modernAbsolutePath = $directoryPub->getAbsolutePath($requestedFile);

                if (file_exists($sourceAbsolutePath)) {
                    // Convert the generated JPG/PNG to the chosen modern format
                    $converted = false;
                    if ($format === 'avif') {
                        $converted = $this->imageHelper->convertToAvif($sourceAbsolutePath, $modernAbsolutePath);
                    } else {
                        $converted = $this->imageHelper->convertToWebp($sourceAbsolutePath, $modernAbsolutePath);
                    }

                    if ($converted) {
                        // Update the response to serve the newly created file
                        if ($response instanceof HttpResponse && method_exists($response, 'setFilePath')) {
                            $response->setFilePath($modernAbsolutePath);

                            // Remove existing Content-Type header and set it to image/$format
                            $response->clearHeader('Content-Type');
                            $response->setHeader('Content-Type', 'image/' . $format);
                        }
                    } else {
                        $this->imageHelper->log("Failed to convert {$sourceAbsolutePath} to {$format}.");
                    }
                } else {
                    $this->imageHelper->log("Generated source file not found: {$sourceAbsolutePath}");
                }

                // Restore the original requested filename state for the subject
                $relativeFileNameProp->setValue($subject, $requestedFile);

                return $response;
            }
        } catch (\Exception $e) {
            $this->imageHelper->log("MediaPlugin exception: " . $e->getMessage());
            return $proceed();
        }

        return $proceed();
    }
}
