<?php
/**
 * @author    Lev Semin <lev@darvin-studio.ru>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Size\Manager;

/**
 * Size manager
 */
interface SizeManagerInterface
{
    /**
     * Saves sizes.
     */
    public function saveSizes();

    /**
     * @return \Darvin\ImageBundle\Size\SizeGroup[]
     */
    public function getAllGroups();

    /**
     * @param string $name Size group name
     *
     * @return \Darvin\ImageBundle\Size\SizeGroup
     */
    public function getGroup($name);

    /**
     * @param string $groupName Size group name
     * @param string $sizeName  Size name
     *
     * @return \Darvin\ImageBundle\Size\Size
     */
    public function getSize($groupName, $sizeName);
}
