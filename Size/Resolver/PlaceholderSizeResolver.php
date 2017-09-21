<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Size\Resolver;

use Darvin\ImageBundle\Size\Manager\SizeManagerInterface;

/**
 * Placeholder image size resolver
 */
class PlaceholderSizeResolver implements SizeResolverInterface
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
     * {@inheritdoc}
     */
    public function findSize($object, $name)
    {
        $size = $this->sizeManager->getSize(null, $name, true);

        return [$size->getWidth(), $size->getHeight()];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsObject($object)
    {
        return null === $object;
    }
}
