<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Size;

/**
 * Size group
 */
class SizeGroup
{
    /**
     * @var \Darvin\ImageBundle\Size\Size[]
     */
    private $sizes;

    /**
     * @param \Darvin\ImageBundle\Size\Size[] $sizes Sizes
     */
    public function __construct(array $sizes = array())
    {
        $this->sizes = array();

        foreach ($sizes as $size) {
            $this->sizes[$size->getName()] = $size;
        }
    }

    /**
     * @return array
     */
    public function getSizeNames()
    {
        return array_keys($this->sizes);
    }

    /**
     * @param string $name Size name
     *
     * @return \Darvin\ImageBundle\Size\Size
     */
    public function findSizeByName($name)
    {
        return isset($this->sizes[$name]) ? $this->sizes[$name] : null;
    }
}
