<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 17.08.15
 * Time: 17:50
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
     * @param string $name Size name
     *
     * @return \Darvin\ImageBundle\Size\Size
     */
    public function findSizeByName($name)
    {
        return isset($this->sizes[$name]) ? $this->sizes[$name] : null;
    }
}
