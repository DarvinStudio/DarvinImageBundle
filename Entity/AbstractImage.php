<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 17.08.15
 * Time: 11:39
 */

namespace Darvin\ImageBundle\Entity;

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
    const CLASS_NAME = 'Darvin\\ImageBundle\\Entity\\AbstractImage';

    /**
     * @var int
     *
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Id
     */
    private $id;

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
