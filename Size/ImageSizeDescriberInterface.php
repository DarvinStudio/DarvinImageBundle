<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Size;

/**
 * Image size describer
 */
interface ImageSizeDescriberInterface
{
    /**
     * @param string|string[]|null $filterSetNames Imagine filter set names
     * @param int                  $width          Width
     * @param int                  $height         Height
     * @param string|null          $entityClass    Image entity class
     *
     * @return string|null
     */
    public function describeSize($filterSetNames = null, int $width = 0, int $height = 0, ?string $entityClass = null): ?string;
}
