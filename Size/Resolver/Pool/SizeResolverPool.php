<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.04.15
 * Time: 14:52
 */

namespace Darvin\ImageBundle\Size\Resolver\Pool;

use Darvin\ImageBundle\Size\Resolver\SizeResolverInterface;

/**
 * Image size resolver pool
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
     * @param \Darvin\ImageBundle\Size\Resolver\SizeResolverInterface $resolver Image size resolver
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

        return null;
    }
}
