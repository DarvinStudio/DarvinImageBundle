<?php
/**
 * @author    Lev Semin <lev@darvin-studio.ru>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Size\Resolver;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\ImageBundle\Size\Manager\SizeManagerInterface;

/**
 * Entity size resolver
 */
class EntitySizeResolver implements SizeResolverInterface
{
    /**
     * @var \Darvin\ImageBundle\Size\Manager\SizeManagerInterface
     */
    private $sizeManager;

    /**
     * @param \Darvin\ImageBundle\Size\Manager\SizeManagerInterface $sizeManager Size manager
     */
    public function __construct(SizeManagerInterface $sizeManager)
    {
        $this->sizeManager = $sizeManager;
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $object Image
     * @param string                                         $name   Size name
     *
     * @return array
     */
    public function findSize($object, $name)
    {
        $size = $object->findSize($name) ?: $this->sizeManager->getSize($object->getSizeGroupName(), $name);

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
