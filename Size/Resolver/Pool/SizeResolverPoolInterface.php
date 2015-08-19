<?php
/**
 * @author    Lev Semin <lev@darvin-studio.ru>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
