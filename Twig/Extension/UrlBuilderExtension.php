<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 18.08.15
 * Time: 14:47
 */

namespace Darvin\ImageBundle\Twig\Extension;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\ImageBundle\UrlBuilder\Filter\ResizeFilter;
use Darvin\ImageBundle\UrlBuilder\UrlBuilderInterface;

/**
 * URL builder Twig extension
 */
class UrlBuilderExtension extends \Twig_Extension
{
    /**
     * @var \Darvin\ImageBundle\UrlBuilder\UrlBuilderInterface
     */
    private $urlBuilder;

    /**
     * @param \Darvin\ImageBundle\UrlBuilder\UrlBuilderInterface $urlBuilder URL builder
     */
    public function __construct(UrlBuilderInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('image_original', array($this->urlBuilder, 'buildUrlToOriginal')),
            new \Twig_SimpleFilter('image_crop', array($this, 'cropImage')),
            new \Twig_SimpleFilter('image_resize', array($this, 'resizeImage')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('image_exists', array($this->urlBuilder, 'fileExists')),
        );
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image               Image
     * @param string                                         $sizeName            Size name
     * @param string                                         $watermarkFilterName Watermark Imagine filter name
     *
     * @return string
     */
    public function cropImage(AbstractImage $image, $sizeName, $watermarkFilterName = null)
    {
        return $this->makeImageThumbnail($image, $sizeName, true, $watermarkFilterName);
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image               Image
     * @param string                                         $sizeName            Size name
     * @param string                                         $watermarkFilterName Watermark Imagine filter name
     *
     * @return string
     */
    public function resizeImage(AbstractImage $image, $sizeName, $watermarkFilterName = null)
    {
        return $this->makeImageThumbnail($image, $sizeName, false, $watermarkFilterName);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'darvin_image_url_builder_extension';
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image Image         Image
     * @param string                                         $sizeName            Size name
     * @param bool                                           $outbound            Is outbound
     * @param string                                         $watermarkFilterName Watermark Imagine filter name
     *
     * @return string
     */
    private function makeImageThumbnail(AbstractImage $image, $sizeName, $outbound, $watermarkFilterName)
    {
        $parameters = array(
            'size_name' => $sizeName,
            'outbound'  => $outbound,
        );

        if (!empty($watermarkFilterName)) {
            $parameters['watermark'] = $watermarkFilterName;
        }

        return $this->urlBuilder->buildUrlToFilter($image, ResizeFilter::NAME, $parameters);
    }
}
