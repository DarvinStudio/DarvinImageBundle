<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Entity\Image;

use Darvin\ImageBundle\Size\Size as SizeModel;
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
     * @ORM\JoinColumn(nullable=false)
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
     *
     * @Assert\GreaterThan(0)
     * @Assert\NotBlank
     */
    private $width;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     *
     * @Assert\GreaterThan(0)
     * @Assert\NotBlank
     */
    private $height;

    /**
     * @param SizeModel $model Size model
     *
     * @return Size
     */
    public static function fromModel(SizeModel $model)
    {
        $size = new self();

        return $size
            ->setName($model->getName())
            ->setWidth($model->getWidth())
            ->setHeight($model->getHeight());
    }

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
