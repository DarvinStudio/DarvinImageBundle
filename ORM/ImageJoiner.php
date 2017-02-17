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

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\Utils\Strings\StringsUtil;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

/**
 * Image joiner
 */
class ImageJoiner implements ImageJoinerInterface
{
    /**
     * {@inheritdoc}
     */
    public function joinImages(QueryBuilder $qb)
    {
        $class = $qb->getRootEntities()[0];

        foreach ($qb->getEntityManager()->getClassMetadata($class)->associationMappings as $mapping) {
            if (ClassMetadata::ONE_TO_ONE !== $mapping['type']
                || !in_array(AbstractImage::class, class_parents($mapping['targetEntity']))
            ) {
                continue;
            }

            $parts = $qb->getDQLParts();
            $rootAlias = $qb->getRootAliases()[0];

            $existingJoins = array_map(function (Join $expr) {
                return $expr->getJoin();
            }, isset($parts['join'][$rootAlias]) ? $parts['join'][$rootAlias] : []);

            $imageJoinAlias = StringsUtil::toUnderscore($mapping['fieldName']);
            $sizesJoinAlias = $imageJoinAlias.'_sizes';

            foreach ([
                sprintf('%s.%s', $rootAlias, $mapping['fieldName']) => $imageJoinAlias,
                sprintf('%s.sizes', $imageJoinAlias)                => $sizesJoinAlias,
            ] as $join => $alias) {
                if (!in_array($join, $existingJoins)) {
                    $qb
                        ->addSelect($alias)
                        ->leftJoin($join, $alias);
                }
            }
        }
    }
}
