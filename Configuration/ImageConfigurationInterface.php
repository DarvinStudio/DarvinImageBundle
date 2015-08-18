<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 18.08.15
 * Time: 11:42
 */

namespace Darvin\ImageBundle\Configuration;

use Darvin\ConfigBundle\Configuration\ConfigurationInterface;

/**
 * Image configuration
 */
interface ImageConfigurationInterface extends ConfigurationInterface
{
    /**
     * @return \Darvin\ImageBundle\Size\Size[]
     */
    public function getSizes();
}
