<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Entity\Image;

use Darvin\ContentBundle\Traits\TranslationTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Image translation
 *
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
class ImageTranslation
{
    use TranslationTrait;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    private $alt;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    private $title;

    /**
     * {@inheritDoc}
     */
    public static function getTranslatableEntityClass(): string
    {
        return AbstractImage::class;
    }

    /**
     * @return string|null
     */
    public function getAlt(): ?string
    {
        return $this->alt;
    }

    /**
     * @param string|null $alt alt
     *
     * @return ImageTranslation
     */
    public function setAlt(?string $alt): ImageTranslation
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title title
     *
     * @return ImageTranslation
     */
    public function setTitle(?string $title): ImageTranslation
    {
        $this->title = $title;

        return $this;
    }
}
