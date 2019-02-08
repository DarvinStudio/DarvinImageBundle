<?php declare(strict_types=1);
/**
 * @author    Lev Semin     <lev@darvin-studio.ru>
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\UrlBuilder\Filter;

/**
 * URL builder filter
 */
interface FilterInterface
{
    /**
     * @param string $imagePathname Image pathname
     * @param array  $parameters    Parameters
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function buildUrl(string $imagePathname, array $parameters = []): string;

    /**
     * @return string
     */
    public function getName(): string;
}
