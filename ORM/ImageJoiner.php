<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\ORM;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\Utils\Strings\StringsUtil;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
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
    public function joinImages(QueryBuilder $qb, ?string $locale = null): void
    {
        $class = $qb->getRootEntities()[0];

        foreach ($qb->getEntityManager()->getClassMetadata($class)->associationMappings as $mapping) {
            if (ClassMetadataInfo::ONE_TO_ONE !== $mapping['type']
                || !in_array(AbstractImage::class, class_parents($mapping['targetEntity']))
            ) {
                continue;
            }

            $parts     = $qb->getDQLParts();
            $rootAlias = $qb->getRootAliases()[0];

            $existingJoins = array_map(function (Join $expr) {
                return $expr->getJoin();
            }, isset($parts['join'][$rootAlias]) ? $parts['join'][$rootAlias] : []);

            foreach ([
                sprintf('%s.%s', $rootAlias, $mapping['fieldName']) => [
                    'alias' => StringsUtil::toUnderscore($mapping['fieldName']),
                ],
                sprintf('%s.translations', $mapping['fieldName']) => [
                    'alias'  => sprintf('%s_translations', StringsUtil::toUnderscore($mapping['fieldName'])),
                    'locale' => $locale,
                ],
            ] as $join => $attr) {
                if (in_array($join, $existingJoins)) {
                    continue;
                }

                $qb
                    ->addSelect($attr['alias'])
                    ->leftJoin($join, $attr['alias']);

                if (null === $locale || !array_key_exists('locale', $attr)) {
                    continue;
                }

                $qb
                    ->andWhere($qb->expr()->orX(
                        sprintf('%s.locale IS NULL', $attr['alias']),
                        sprintf('%s.locale = :locale', $attr['alias'])
                    ))
                    ->setParameter('locale', $locale);
            }
        }
    }
}
