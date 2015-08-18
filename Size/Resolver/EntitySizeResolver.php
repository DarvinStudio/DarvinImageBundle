<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.04.15
 * Time: 14:45
 */

namespace Darvin\ImageBundle\Size\Resolver;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\ImageBundle\SizeManager\SizeManagerInterface;

/**
 * Entity size resolver
 */
class EntitySizeResolver implements SizeResolverInterface
{
    /**
     * @var \Darvin\ImageBundle\SizeManager\SizeManagerInterface
     */
    private $sizeManager;

    /**
     * @param \Darvin\ImageBundle\SizeManager\SizeManagerInterface $sizeManager Size manager
     */
//    public function __construct(SizeManagerInterface $sizeManager)
//    {
//        $this->sizeManager = $sizeManager;
//    }

    /**
     * {@inheritdoc}
     */
    public function findSize($object, $name)
    {
        $size = $object->findSize($name) ?: $this->sizeManager->getSize($object->getSizeBlockName(), $name);

        return array($size->getWidth(), $size->getHeight());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsObject($object)
    {
        return $object instanceof AbstractImage;
    }
}
