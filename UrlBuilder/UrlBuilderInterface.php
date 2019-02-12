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
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage|null $image      Image
     * @param string                                              $filterName Filter name
     * @param array                                               $parameters Parameters
     * @param string|null                                         $fallback   Fallback
     *
     * @return string|null
     */
    public function buildFilteredUrl(?AbstractImage $image, string $filterName, array $parameters = [], ?string $fallback = null): ?string;

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage|null $image       Image
     * @param bool                                                $prependHost Whether to prepend host
     * @param string|null                                         $fallback    Fallback
     *
     * @return string|null
     */
    public function buildOriginalUrl(?AbstractImage $image, bool $prependHost = true, ?string $fallback = null): ?string;

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage|null $image Image
     *
     * @return bool
     */
    public function isActive(?AbstractImage $image): bool;
}
