<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.04.15
 * Time: 12:25
 */

namespace Darvin\ImageBundle\UrlBuilder\Filter;

/**
 * URL builder filter
 */
interface FilterInterface
{
    /**
     * @param string $imagePathname Image pathname
     * @param array  $parameters    Parameters
     *
     * @return string
     */
    public function buildUrl($imagePathname, array $parameters);

    /**
     * @return string
     */
    public function getName();
}
