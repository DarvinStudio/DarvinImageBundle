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

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Archiver
 */
class Archiver
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var string
     */
    private $filenameSuffix;

    /**
     * @var string
     */
    private $uploadDir;

    /**
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack   Request stack
     * @param string                                         $cacheDir       Cache directory
     * @param string                                         $filenameSuffix Filename suffix
     * @param string                                         $uploadDir      Upload directory
     */
    public function __construct(RequestStack $requestStack, $cacheDir, $filenameSuffix, $uploadDir)
    {
        $this->requestStack = $requestStack;
        $this->cacheDir = $cacheDir;
        $this->filenameSuffix = $filenameSuffix;
        $this->uploadDir = $uploadDir;
    }

    /**
     * @return string
     */
    public function archive()
    {
        $this->prepareCacheDir();

        $pathname = $this->buildPathname();

        return $this->buildZip($pathname);
    }

    /**
     * @param string $pathname Pathname
     *
     * @return string
     * @throws \Darvin\ImageBundle\Archive\ArchiveException
     */
    private function buildZip($pathname)
    {
        $zip = new \ZipArchive();

        if (true !== $zip->open($pathname, \ZipArchive::CREATE)) {
            throw new ArchiveException(sprintf('Unable to create image archive "%s".', $pathname));
        }

        $finder = (new Finder())->in($this->uploadDir);

        foreach ($finder->directories() as $dir) {
            if (!$zip->addEmptyDir($dir->getRelativePathname())) {
                throw new ArchiveException(
                    sprintf('Unable to create directory "%s" in image archive "%s".', $dir->getRelativePathname(), $pathname)
                );
            }
        }
        foreach ($finder->files() as $file) {
            if (!$zip->addFile($file->getPathname(), $file->getRelativePathname())) {
                throw new ArchiveException(
                    sprintf('Unable to add file "%s" to image archive "%s".', $file->getPathname(), $pathname)
                );
            }
        }
        if (!$zip->close()) {
            throw new ArchiveException(sprintf('Unable to close image archive "%s".', $pathname));
        }

        return $pathname;
    }

    /**
     * @throws \Darvin\ImageBundle\Archive\ArchiveException
     */
    private function prepareCacheDir()
    {
        $fs = new Filesystem();

        if ($fs->exists($this->cacheDir)) {
            return;
        }
        try {
            $fs->mkdir($this->cacheDir);
        } catch (IOException $ex) {
            throw new ArchiveException(
                sprintf('Unable to create image archive cache dir "%s": "%s".', $this->cacheDir, $ex->getMessage())
            );
        }
    }

    /**
     * @return string
     */
    private function buildPathname()
    {
        $parts = array_merge(preg_split('/[^0-9a-z]+/i', $this->getHost()), [
            $this->filenameSuffix,
            (new \DateTime())->format('dmY_Hi'),
        ]);

        return sprintf('%s/%s.zip', $this->cacheDir, implode('_', $parts));
    }

    /**
     * @return string
     */
    private function getHost()
    {
        $request = $this->requestStack->getCurrentRequest();

        return !empty($request) ? $request->getHost() : null;
    }
}
