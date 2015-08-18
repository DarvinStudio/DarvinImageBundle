<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 05.04.15
 * Time: 8:39
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

    /**
     * @param string $path Path
     *
     * @return \Darvin\ImageBundle\Size\Size
     */
    public function getSizeByPath($path);
}
