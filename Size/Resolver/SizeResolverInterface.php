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
