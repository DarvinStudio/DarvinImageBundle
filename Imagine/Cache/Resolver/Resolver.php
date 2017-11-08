<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Imagine\Cache\Resolver;

use Liip\ImagineBundle\Imagine\Cache\Resolver\WebPathResolver;

/**
 * Imagine cache resolver
 */
class Resolver extends WebPathResolver
{
    /**
     * {@inheritdoc}
     */
    protected function getFileUrl($path, $filter)
    {
        return implode(DIRECTORY_SEPARATOR, [$this->cachePrefix, str_replace('_', '/', $filter), preg_replace('/.*\//', '', $path)]);
    }
}
