<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Imagine\Cache\Warmer;

use Darvin\ImageBundle\Entity\Image\AbstractImage;

/**
 * Imagine cache warmer
 */
interface ImagineCacheWarmerInterface
{
    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image Image
     */
    public function warmImageCache(AbstractImage $image): void;
}
