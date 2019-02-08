<?php declare(strict_types=1);
/**
 * @author    Lev Semin     <lev@darvin-studio.ru>
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
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
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage|null $image   Image
     * @param bool                                                $addHost Whether to add host to URL
     *
     * @return string
     * @throws \Darvin\ImageBundle\UrlBuilder\Exception\ImageNotFoundException
     */
    public function buildUrlToOriginal(?AbstractImage $image, bool $addHost = false): string;

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage|null $image      Image
     * @param string                                              $filterName Filter name
     * @param array                                               $parameters Parameters
     *
     * @return string
     * @throws \Darvin\ImageBundle\UrlBuilder\Exception\ImageNotFoundException
     */
    public function buildUrlToFilter(?AbstractImage $image, string $filterName, array $parameters = []): string;

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage|null $image Image
     *
     * @return bool
     */
    public function fileExists(?AbstractImage $image): bool;
}
