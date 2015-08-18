<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.04.15
 * Time: 14:42
 */

namespace Darvin\ImageBundle\Size\Resolver;

/**
 * Size resolver
 */
interface SizeResolverInterface
{
    /**
     * @param object $object Image object
     * @param string $name   Size name
     *
     * @return array
     */
    public function findSize($object, $name);

    /**
     * @param object $object Image object
     *
     * @return bool
     */
    public function supportsObject($object);
}
