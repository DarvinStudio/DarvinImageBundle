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
     * @var array
     */
    private $formats;

    /**
     * @param array $formats Output formats
     */
    public function setFormats(array $formats): void
    {
        $this->formats = array_filter($formats, function (array $format): bool {
            return $format['enabled'];
        });
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    protected function getFileUrl($path, $filter): string
    {
        $path   = (string)$path;
        $filter = (string)$filter;

        $filename    = preg_replace('/^.*\//', '', $path);
        $filterParts = explode('__', $filter);

        if (2 === count($filterParts) && isset($this->formats[$filterParts[1]])) {
            $filename = preg_replace('/(^.+\.).+$/', sprintf('$1%s', $filterParts[1]), $filename);
        }

        return implode(DIRECTORY_SEPARATOR, [$this->cachePrefix, $this->getFilterCacheDir($filter), $filename]);
    }

    /**
     * @param string|null $filter Filter name
     *
     * @return string
     */
    private function getFilterCacheDir(?string $filter): string
    {
        $filter = (string)$filter;

        $parts = explode('__', $filter);

        if (2 === count($parts) && isset($this->formats[$parts[1]])) {
            $filter = $parts[0];
        }

        return str_replace('_', '/', $filter);
    }
}
