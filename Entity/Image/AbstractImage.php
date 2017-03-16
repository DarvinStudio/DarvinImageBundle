<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2016, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Entity\Image;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Image abstract implementation
 *
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\Table(name="image")
 *
 * @Vich\Uploadable
 */
abstract class AbstractImage
{
    const PROPERTY_FILE = 'file';

    /**
     * @var int
     *
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Id
     */
    private $id;

    /**
     * @var \Darvin\ImageBundle\Entity\Image\Size[]|\Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Darvin\ImageBundle\Entity\Image\Size", mappedBy="image", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     *
     * @Assert\Valid
     */
    private $sizes;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default"=1})
     */
    private $enabled;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(groups={"AdminUpdateProperty"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $extension;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $filename;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $width;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $height;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     *
     * @Gedmo\SortablePosition
     */
    private $position;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @var \Symfony\Component\HttpFoundation\File\File
     *
     * @Vich\UploadableField(mapping="darvin_image", fileNameProperty="filename")
     *
     * @Assert\Image
     */
    private $file;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sizes = new ArrayCollection();
        $this->enabled = true;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    abstract public function getSizeGroupName();

    /**
     * @param string $name Size name
     *
     * @return \Darvin\ImageBundle\Entity\Image\Size
     */
    public function findSize($name)
    {
        foreach ($this->sizes as $size) {
            if ($size->getName() === $name) {
                return $size;
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function getDimensions()
    {
        return $this->width.'x'.$this->height;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\Size[]|\Doctrine\Common\Collections\Collection $sizes sizes
     *
     * @return AbstractImage
     */
    public function setSizes(Collection $sizes)
    {
        foreach ($sizes as $size) {
            $size->setImage($this);
        }

        $this->sizes = $sizes;

        return $this;
    }

    /**
     * @return \Darvin\ImageBundle\Entity\Image\Size[]|\Doctrine\Common\Collections\Collection
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\Size $size Size
     *
     * @return AbstractImage
     */
    public function addSize(Size $size)
    {
        if (!$this->sizes->contains($size)) {
            $size->setImage($this);
            $this->sizes->add($size);
        }

        return $this;
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\Size $size Size
     *
     * @return AbstractImage
     */
    public function removeSize(Size $size)
    {
        if ($this->sizes->contains($size)) {
            $size->setImage(null);
            $this->sizes->removeElement($size);
        }

        return $this;
    }

    /**
     * @param boolean $enabled enabled
     *
     * @return AbstractImage
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param string $name name
     *
     * @return AbstractImage
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
     * @param string $extension extension
     *
     * @return AbstractImage
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param string $filename filename
     *
     * @return AbstractImage
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param int $width width
     *
     * @return AbstractImage
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
     * @return AbstractImage
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

    /**
     * @param int $position position
     *
     * @return AbstractImage
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param \DateTime $updatedAt updatedAt
     *
     * @return AbstractImage
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\File $file file
     *
     * @return AbstractImage
     */
    public function setFile(File $file = null)
    {
        $this->file = $file;

        if (!empty($file)) {
            $size = @getimagesize($file->getPathname());

            if (is_array($size)) {
                list($this->width, $this->height) = $size;
            }

            $this->refreshUpdatedAt();
        }

        return $this;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return AbstractImage
     */
    protected function refreshUpdatedAt()
    {
        $this->updatedAt = new \DateTime();

        return $this;
    }
}
