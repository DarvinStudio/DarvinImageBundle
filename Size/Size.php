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

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Size
 */
class Size
{
    const CLASS_NAME = 'Darvin\\ImageBundle\\Size\\Size';

    /**
     * @var string
     *
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var int
     *
     * @Assert\GreaterThan(0)
     * @Assert\NotBlank
     */
    private $width;

    /**
     * @var int
     *
     * @Assert\GreaterThan(0)
     * @Assert\NotBlank
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
