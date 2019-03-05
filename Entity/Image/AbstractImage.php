<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Entity\Image;

use Darvin\ContentBundle\Traits\TranslatableTrait;
use Darvin\ImageBundle\Validation\Constraints as DarvinImageAssert;
use Darvin\Utils\Mapping\Annotation\Clonable;
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
 * @Clonable\Clonable(copyingPolicy="ALL")
 *
 * @Vich\Uploadable
 *
 * @method string|null getAlt()
 * @method string|null getTitle()
 */
abstract class AbstractImage
{
    use TranslatableTrait;

    public const PROPERTY_FILE = 'file';

    /**
     * @var int
     *
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue
     * @ORM\Id
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank(groups={"AdminUpdateProperty"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $extension;

    /**
     * @var string
     *
     * @ORM\Column
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
     * @var \Symfony\Component\HttpFoundation\File\File|null
     *
     * @Vich\UploadableField(mapping="darvin_image", fileNameProperty="filename")
     *
     * @DarvinImageAssert\DarvinImage
     */
    private $file;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->enabled   = true;
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $alt = (string)$this->getAlt();

        if ('' !== $alt) {
            return $alt;
        }

        $title = (string)$this->getTitle();

        if ('' !== $title) {
            return $title;
        }

        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationEntityClass(): string
    {
        return ImageTranslation::class;
    }

    /**
     * @return string
     */
    abstract public static function getUploadDir(): string;

    /**
     * @return string
     */
    public function getDimensions(): string
    {
        return sprintf('%dx%d', $this->width, $this->height);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\File|null $file file
     *
     * @return AbstractImage
     */
    public function setFile(?File $file): AbstractImage
    {
        if (!empty($file)) {
            $size = @getimagesize($file->getPathname());

            if (is_array($size)) {
                list($this->width, $this->height) = $size;
            }

            $this->updatedAt = new \DateTime();
        }

        $this->file = $file;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled enabled
     *
     * @return AbstractImage
     */
    public function setEnabled(?bool $enabled): AbstractImage
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name name
     *
     * @return AbstractImage
     */
    public function setName(?string $name): AbstractImage
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtension(): ?string
    {
        return $this->extension;
    }

    /**
     * @param string $extension extension
     *
     * @return AbstractImage
     */
    public function setExtension(?string $extension): AbstractImage
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string $filename filename
     *
     * @return AbstractImage
     */
    public function setFilename(?string $filename): AbstractImage
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return int
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * @param int $width width
     *
     * @return AbstractImage
     */
    public function setWidth(?int $width): AbstractImage
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return int
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * @param int $height height
     *
     * @return AbstractImage
     */
    public function setHeight(?int $height): AbstractImage
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @param int $position position
     *
     * @return AbstractImage
     */
    public function setPosition(?int $position): AbstractImage
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\File\File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }
}
