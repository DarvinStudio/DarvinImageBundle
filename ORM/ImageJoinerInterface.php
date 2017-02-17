<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\ORM;

use Doctrine\ORM\QueryBuilder;

/**
 * Image joiner
 */
interface ImageJoinerInterface
{
    /**
     * @param \Doctrine\ORM\QueryBuilder $qb Query builder
     */
    public function joinImages(QueryBuilder $qb);
}
