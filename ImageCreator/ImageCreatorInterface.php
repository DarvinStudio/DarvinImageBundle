<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.04.15
 * Time: 22:11
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
