<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Twig\Extension;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\ImageBundle\UrlBuilder\Filter\DirectImagineFilter;
use Darvin\ImageBundle\UrlBuilder\Filter\ResizeFilter;
use Darvin\ImageBundle\UrlBuilder\UrlBuilderInterface;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Psr\Log\LoggerInterface;

/**
 * URL builder Twig extension
 */
class UrlBuilderExtension extends \Twig_Extension
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Darvin\ImageBundle\UrlBuilder\UrlBuilderInterface
     */
    private $urlBuilder;

    /**
     * @param \Psr\Log\LoggerInterface                           $logger     Logger
     * @param \Darvin\ImageBundle\UrlBuilder\UrlBuilderInterface $urlBuilder URL builder
     */
    public function __construct(LoggerInterface $logger, UrlBuilderInterface $urlBuilder)
    {
        $this->logger = $logger;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('image_original', [$this, 'buildUrlToOriginal']),
            new \Twig_SimpleFilter('image_crop', [$this, 'cropImage']),
            new \Twig_SimpleFilter('image_resize', [$this, 'resizeImage']),
            new \Twig_SimpleFilter('image_imagine', [$this, 'buildImagine'])
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('image_exists', [$this->urlBuilder, 'fileExists']),
        ];
    }

    /**
     * @param AbstractImage|null $image
     * @param string $filterName
     * @return null|string
     */
    public function buildImagine(AbstractImage $image = null, $filterName = null)
    {
        if (empty($image)) {
            return null;
        }
        try {
            return $this->urlBuilder->buildUrlToFilter($image, DirectImagineFilter::NAME, [
                DirectImagineFilter::FILTER_NAME_PARAM => $filterName
            ], false);
        } catch (NotLoadableException $ex) {
            $this->logError($image, $ex);

            return null;
        }
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image    Image
     * @param bool                                           $absolute Whether to build absolute URL
     *
     * @return string
     */
    public function buildUrlToOriginal(AbstractImage $image = null, $absolute = false)
    {
        if (empty($image)) {
            return null;
        }
        try {
            return $this->urlBuilder->buildUrlToOriginal($image, $absolute);
        } catch (NotLoadableException $ex) {
            $this->logError($image, $ex);

            return null;
        }
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image               Image
     * @param string                                         $sizeName            Size name
     * @param string                                         $watermarkFilterName Watermark Imagine filter name
     *
     * @return string
     */
    public function cropImage(AbstractImage $image = null, $sizeName, $watermarkFilterName = null)
    {
        return $this->makeImageResize($image, $sizeName, true, $watermarkFilterName);
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image               Image
     * @param string                                         $sizeName            Size name
     * @param string                                         $watermarkFilterName Watermark Imagine filter name
     *
     * @return string
     */
    public function resizeImage(AbstractImage $image = null, $sizeName, $watermarkFilterName = null)
    {
        return $this->makeImageResize($image, $sizeName, false, $watermarkFilterName);
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image               Image
     * @param string                                         $sizeName            Size name
     * @param bool                                           $outbound            Is outbound
     * @param string                                         $watermarkFilterName Watermark Imagine filter name
     *
     * @return string
     */
    private function makeImageResize(AbstractImage $image = null, $sizeName, $outbound, $watermarkFilterName)
    {
        $parameters = [
            'size_name' => $sizeName,
            'outbound'  => $outbound,
        ];

        if (!empty($image) && !empty($watermarkFilterName)) {
            $parameters['watermark'] = $watermarkFilterName;
        }

        try {
            return $this->urlBuilder->buildUrlToFilter($image, ResizeFilter::NAME, $parameters);
        } catch (NotLoadableException $ex) {
            $this->logError($image, $ex);

            return null;
        }
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image Image
     * @param \Exception                                     $ex    Exception
     */
    private function logError(AbstractImage $image = null, \Exception $ex)
    {
        if (empty($image)) {
            $this->logger->error(sprintf('Unable to build URL for placeholder image: "%s".', $ex->getMessage()));

            return;
        }

        $this->logger->error(sprintf('Unable to build URL for image with ID "%d": "%s".', $image->getId(), $ex->getMessage()));
    }
}
