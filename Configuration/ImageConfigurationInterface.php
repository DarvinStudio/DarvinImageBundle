<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Configuration;

@trigger_error('The "'.__NAMESPACE__.'\ImageConfigurationInterface" is deprecated. You should stop using it, as it will soon be removed.', E_USER_DEPRECATED);

use Darvin\ConfigBundle\Configuration\ConfigurationInterface;

/**
 * Image configuration
 *
 * @deprecated
 */
interface ImageConfigurationInterface extends ConfigurationInterface
{
    /**
     * @return \Darvin\ImageBundle\Size\Size[]
     */
    public function getImageSizes();

    /**
     * @return bool
     */
    public function isImageSizesGlobal();

    /**
     * @return string
     */
    public function getImageSizeGroupName();
}
