<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Entity\Image;

use Darvin\ContentBundle\Traits\TranslatableTrait;
use Darvin\FileBundle\Entity\AbstractFile;
use Darvin\ImageBundle\Validation\Constraints as DarvinImageAssert;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Image
 *
 * @ORM\Entity
 *
 * @method string|null getAlt()
 * @method string|null getTitle()
 */
abstract class AbstractImage extends AbstractFile implements TranslatableInterface
{
    use TranslatableTrait;

    protected const VECTOR_EXTENSIONS = [
        'svg',
    ];

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $width;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $height;

    /**
     * {@inheritDoc}
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

        return parent::__toString();
    }

    /**
     * {@inheritDoc}
     */
    public static function getBaseUploadDir(): string
    {
        return 'images';
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationEntityClass(): string
    {
        return ImageTranslation::class;
    }

    /**
     * @DarvinImageAssert\DarvinImage
     *
     * @return \Symfony\Component\HttpFoundation\File\File|null
     */
    public function getFile(): ?File
    {
        return parent::getFile();
    }

    /**
     * {@inheritDoc}
     */
    public function setFile(?File $file): AbstractFile
    {
        if (null !== $file) {
            $size = @getimagesize($file->getPathname());

            if (is_array($size)) {
                list($this->width, $this->height) = $size;
            }
        }

        return parent::setFile($file);
    }

    /**
     * @return string|null
     */
    public function getDimensions(): ?string
    {
        if (null !== $this->width && null !== $this->height) {
            return implode('x', [$this->width, $this->height]);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isVector(): bool
    {
        return in_array($this->extension, self::VECTOR_EXTENSIONS);
    }

    /**
     * @return int|null
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }
}
