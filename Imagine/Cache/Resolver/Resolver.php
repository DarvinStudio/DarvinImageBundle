<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2019, Darvin Studio
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
    public function remove(array $paths, array $filters): void
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
     * {@inheritDoc}
     */
    protected function getFilePath($path, $filter): string
    {
        return implode(DIRECTORY_SEPARATOR, [$this->webRoot, $this->getFileUrl($path, $filter)]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFileUrl($path, $filter): string
    {
        return implode(DIRECTORY_SEPARATOR, [$this->cachePrefix, $this->getFilterCacheDir($filter), preg_replace('/.*\//', '', $path ?: '')]);
    }

    /**
     * @param string|null $filter Filter name
     *
     * @return string
     */
    private function getFilterCacheDir(?string $filter): string
    {
        return str_replace('_', '/', $filter ?: '');
    }
}
