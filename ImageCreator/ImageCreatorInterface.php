<?php
/**
 * @author    Lev Semin <lev@darvin-studio.ru>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\ImageCreator;

/**
 * Image creator
 */
interface ImageCreatorInterface
{
    /**
     * @param string $imagePathname Image pathname
     * @param array  $filters       Filters
     *
     * @return string
     */
    public function createImage($imagePathname, array $filters = array());
}
