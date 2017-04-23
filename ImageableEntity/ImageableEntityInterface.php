<?php
/**
 * @author    Lev Semin <lev@darvin-studio.ru>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\ImageableEntity;
use Darvin\ImageBundle\Entity\Image\AbstractImage;


/**
 * Interface ImageableEntityInterface
 * @package Darvin\ImageBundle\ImageableEntity
 * 
 * This interface provides getImage method
 * It returns main image entity for present the object 
 */
interface ImageableEntityInterface
{
    /**
     * @return AbstractImage|null an image that presents object 
     */
    public function getImage();
}