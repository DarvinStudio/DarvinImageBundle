<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 18.08.15
 * Time: 17:17
 */

namespace Darvin\ImageBundle\Namer;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

/**
 * Directory namer
 */
class DirectoryNamer implements DirectoryNamerInterface
{
    /**
     * {@inheritdoc}
     */
    public function directoryName($object, PropertyMapping $mapping)
    {
        return $object instanceof AbstractImage ? $object->getSizeGroupName() : null;
    }
}
