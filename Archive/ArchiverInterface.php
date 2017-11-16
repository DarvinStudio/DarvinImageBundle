<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016, Darvin Studio
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
     * @throws \Darvin\ImageBundle\Archive\ArchiveException
     */
    public function archive();

    /**
     * @param string $filename Archive filename
     *
     * @return string
     */
    public function buildPathname($filename);
}
