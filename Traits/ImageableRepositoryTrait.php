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
     * @param string                     $alias     Alias
     *
     * @return self
     */
    protected function joinImage(QueryBuilder $qb, ?string $locale = null, bool $addSelect = true, string $join = 'o.image', string $alias = 'image')
    {
        $translationsAlias = sprintf('%s_translations', $alias);

        $qb
            ->leftJoin($join, $alias)
            ->leftJoin(sprintf('%s.translations', $alias), $translationsAlias);

        if (null !== $locale) {
            $qb
                ->andWhere($qb->expr()->orX(
                    sprintf('%s.locale IS NULL', $translationsAlias),
                    sprintf('%s.locale = :locale', $translationsAlias)
                ))
                ->setParameter('locale', $locale);
        }
        if ($addSelect) {
            $qb
                ->addSelect($alias)
                ->addSelect($translationsAlias);
        }

        return $this;
    }
}
