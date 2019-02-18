<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Traits;

use Doctrine\ORM\QueryBuilder;

/**
 * Imageable entity repository trait
 */
trait ImageableRepositoryTrait
{
    /**
     * @param \Doctrine\ORM\QueryBuilder $qb        Query builder
     * @param string|null                $locale    Locale
     * @param bool                       $addSelect Whether to add select
     * @param string                     $join      Join
     */
    protected function innerJoinImage(QueryBuilder $qb, ?string $locale = null, bool $addSelect = true, string $join = 'o.image')
    {
        $qb
            ->innerJoin($join, 'image')
            ->innerJoin('image.translations', 'image_translations');

        if (null !== $locale) {
            $qb
                ->andWhere('image_translations.locale = :locale')
                ->setParameter('locale', $locale);
        }
        if ($addSelect) {
            $qb
                ->addSelect('image')
                ->addSelect('image_translations');
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb        Query builder
     * @param string|null                $locale    Locale
     * @param bool                       $addSelect Whether to add select
     * @param string                     $join      Join
     */
    protected function leftJoinImage(QueryBuilder $qb, ?string $locale = null, bool $addSelect = true, string $join = 'o.image')
    {
        $qb
            ->leftJoin($join, 'image')
            ->leftJoin('image.translations', 'image_translations');

        if (null !== $locale) {
            $qb
                ->andWhere($qb->expr()->orX(
                    'image_translations.locale IS NULL',
                    'image_translations.locale = :locale'
                ))
                ->setParameter('locale', $locale);
        }
        if ($addSelect) {
            $qb
                ->addSelect('image')
                ->addSelect('image_translations');
        }
    }
}
