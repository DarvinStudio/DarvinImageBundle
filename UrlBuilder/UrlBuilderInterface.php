<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.04.15
 * Time: 12:21
 */

namespace Darvin\ImageBundle\UrlBuilder;

use Darvin\ImageBundle\Entity\Image\AbstractImage;

/**
 * URL builder
 */
interface UrlBuilderInterface
{
    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image Image
     *
     * @return bool
     */
    public function hasFile(AbstractImage $image = null);

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image Image
     *
     * @return string
     * @throws \Darvin\ImageBundle\UrlBuilder\Exception\ImageNotFoundException
     */
    public function buildUrlToOriginal(AbstractImage $image);

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image      Image
     * @param string                                         $filterName Filter name
     * @param array                                          $parameters Parameters
     *
     * @return string
     * @throws \Darvin\ImageBundle\UrlBuilder\Exception\ImageNotFoundException
     * @throws \Darvin\ImageBundle\UrlBuilder\Exception\FilterNotFoundException
     */
    public function buildUrlToFilter(AbstractImage $image, $filterName, array $parameters = array());
}
