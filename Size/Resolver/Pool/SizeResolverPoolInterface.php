<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.04.15
 * Time: 14:51
 */

namespace Darvin\ImageBundle\Size\Resolver\Pool;

/**
 * Size resolver pool
 */
interface SizeResolverPoolInterface
{
    /**
     * @param object $object Object
     *
     * @return \Darvin\ImageBundle\Size\Resolver\SizeResolverInterface
     */
    public function getForObject($object);
}
