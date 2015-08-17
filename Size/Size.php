<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 17.08.15
 * Time: 14:26
 */

namespace Darvin\ImageBundle\Size;

/**
 * Image size
 */
class Size
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $defaultWidth;

    /**
     * @var int
     */
    private $defaultHeight;

    /**
     * @param string $name          Size name
     * @param int    $defaultWidth  Default width
     * @param int    $defaultHeight Default height
     */
    public function __construct($name, $defaultWidth, $defaultHeight)
    {
        $this->name = $name;
        $this->defaultWidth = $defaultWidth;
        $this->defaultHeight = $defaultHeight;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getDefaultWidth()
    {
        return $this->defaultWidth;
    }

    /**
     * @return int
     */
    public function getDefaultHeight()
    {
        return $this->defaultHeight;
    }
}
