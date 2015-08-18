<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 18.08.15
 * Time: 11:42
 */

namespace Darvin\ImageBundle\Configuration;

/**
 * Image configuration
 */
interface ImageConfigurationInterface
{
    /**
     * @return \Darvin\ImageBundle\Size\Size[]
     */
    public function getSizes();
}
