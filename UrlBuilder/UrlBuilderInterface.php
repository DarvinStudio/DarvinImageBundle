<?php
/**
 * @author    Lev Semin <lev@darvin-studio.ru>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
     * @return string
     */
    public function buildUrlToOriginal(AbstractImage $image);

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image      Image
     * @param string                                         $filterName Filter name
     * @param array                                          $parameters Parameters
     *
     * @return string
     */
    public function buildUrlToFilter(AbstractImage $image, $filterName, array $parameters = array());

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image Image
     *
     * @return bool
     */
    public function fileExists(AbstractImage $image = null);
}
