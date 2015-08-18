<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 17.08.15
 * Time: 14:29
 */

namespace Darvin\ImageBundle\Entity\Image;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Image size
 *
 * @ORM\Entity
 * @ORM\Table(name="image_size")
 */
class Size
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Id
     */
    private $id;

    /**
     * @var \Darvin\ImageBundle\Entity\Image\AbstractImage
     *
     * @ORM\ManyToOne(targetEntity="Darvin\ImageBundle\Entity\Image\AbstractImage", inversedBy="sizes")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     * @Assert\NotBlank
     */
    private $width;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     * @Assert\NotBlank
     */
    private $height;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image image
     *
     * @return Size
     */
    public function setImage(AbstractImage $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return \Darvin\ImageBundle\Entity\Image\AbstractImage
     */
    public function getImage()
    {
        return $this->image;
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
