<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Twig\Extension;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Image Twig extension
 */
class ImageExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('image_alt', [$this, 'getAlt']),
            new TwigFilter('image_title', [$this, 'getTitle']),
        ];
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage|null $image    Image
     * @param string|null                                         $fallback Fallback
     *
     * @return string|null
     */
    public function getAlt(?AbstractImage $image, ?string $fallback = null): ?string
    {
        if (!empty($image) && null !== $image->getAlt()) {
            return $image->getAlt();
        }

        return $fallback;
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage|null $image    Image
     * @param string|null                                         $fallback Fallback
     *
     * @return string|null
     */
    public function getTitle(?AbstractImage $image, ?string $fallback = null): ?string
    {
        if (!empty($image) && null !== $image->getTitle()) {
            return $image->getTitle();
        }

        return $fallback;
    }
}
