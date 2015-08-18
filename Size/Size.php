<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 17.08.15
 * Time: 14:26
 */

namespace Darvin\ImageBundle\Size;

/**
 * Size
 */
class Size
{
    const CLASS_NAME = 'Darvin\\ImageBundle\\Size\\Size';

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @param string $name   Name
     * @param int    $width  Width
     * @param int    $height Height
     */
    public function __construct($name = null, $width = null, $height = null)
    {
        $this->name = $name;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @param string $name name
     *
     * @return Size
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param int $width width
     *
     * @return Size
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $height height
     *
     * @return Size
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }
}
