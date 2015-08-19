<?php
/**
 * @author    Lev Semin     <lev@darvin-studio.ru>
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Size\Resolver\Pool;

use Darvin\ImageBundle\Size\Resolver\SizeResolverException;
use Darvin\ImageBundle\Size\Resolver\SizeResolverInterface;
use Doctrine\Common\Util\ClassUtils;

/**
 * Size resolver pool
 */
class SizeResolverPool implements SizeResolverPoolInterface
{
    /**
     * @var \Darvin\ImageBundle\Size\Resolver\SizeResolverInterface
     */
    private $resolvers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->resolvers = array();
    }

    /**
     * @param \Darvin\ImageBundle\Size\Resolver\SizeResolverInterface $resolver Size resolver
     */
    public function add(SizeResolverInterface $resolver)
    {
        $this->resolvers[] = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getForObject($object)
    {
        /** @var \Darvin\ImageBundle\Size\Resolver\SizeResolverInterface $resolver */
        foreach ($this->resolvers as $resolver) {
            if ($resolver->supportsObject($object)) {
                return $resolver;
            }
        }

        throw new SizeResolverException(
            sprintf('Unable to find size resolver for object "%s".', ClassUtils::getClass($object))
        );
    }
}
