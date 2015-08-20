<?php
/**
 * @author    Lev Semin     <lev@darvin-studio.ru>
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
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
     * @param string $groupName Size group name
     * @param string $sizeName  Size name
     *
     * @return \Darvin\ImageBundle\Size\Size
     */
    public function getSize($groupName, $sizeName);
}
