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
     * {@inheritDoc}
     */
    public static function getTranslatableEntityClass(): string
    {
        return AbstractImage::class;
    }
}
