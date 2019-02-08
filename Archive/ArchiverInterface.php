<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Archive;

/**
 * Archiver
 */
interface ArchiverInterface
{
    /**
     * @return string Archive filename
     * @throws \RuntimeException
     */
    public function archive(): string;

    /**
     * @param string $filename Archive filename
     *
     * @return string
     */
    public function buildPathname(string $filename): string;
}
