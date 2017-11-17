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
    public function remove(array $paths, array $filters)
    {
        if (!empty($paths)) {
            parent::remove($paths, $filters);

            return;
        }
        if (empty($filters)) {
            return;
        }

        $cacheDirs = [];

        foreach ($filters as $filter) {
            $cacheDirs[] = $this->cacheRoot.DIRECTORY_SEPARATOR.$this->getFilterCacheDir($filter);
        }

        $this->filesystem->remove($cacheDirs);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFileUrl($path, $filter)
    {
        return implode(DIRECTORY_SEPARATOR, [$this->cachePrefix, $this->getFilterCacheDir($filter), preg_replace('/.*\//', '', $path)]);
    }

    /**
     * @param string $filter Filter name
     *
     * @return string
     */
    private function getFilterCacheDir($filter)
    {
        return str_replace('_', '/', $filter);
    }
}
