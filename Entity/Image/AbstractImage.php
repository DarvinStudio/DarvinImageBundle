<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 17.08.15
 * Time: 11:39
 */

namespace Darvin\ImageBundle\Entity\Image;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Image abstract implementation
 *
 * @ORM\Entity
 * @ORM\Table(name="image")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @Vich\Uploadable
 */
abstract class AbstractImage
{
    const CLASS_NAME = 'Darvin\\ImageBundle\\Entity\\Image\\AbstractImage';

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
     * @ORM\OneToMany(targetEntity="Darvin\ImageBundle\Entity\Image\Size", mappedBy="image", cascade={"persist"}, orphanRemoval=true)
     */
    private $sizes;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $filename;

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
     */
    private $file;

    /**
     * @param \Darvin\ImageBundle\Entity\Image\Size[] $sizes Sizes
     */
    public function __construct(array $sizes = array())
    {
        $this->sizes = new ArrayCollection($sizes);
    }

    /**
     * @return \Darvin\ImageBundle\Size\Size[]
     */
    abstract public function getDefaultSizes();

    /**
     * @return string
     */
    abstract public function getSizeBlockName();

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
     * Refreshes updated at datetime.
     */
    public function refreshUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
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
}
