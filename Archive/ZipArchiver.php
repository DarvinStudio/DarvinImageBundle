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

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * ZIP archiver
 */
class ZipArchiver implements ArchiverInterface
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var string
     */
    private $filenameSuffix;

    /**
     * @var string
     */
    private $tmpDir;

    /**
     * @var string
     */
    private $uploadDir;

    /**
     * @param \Symfony\Component\Filesystem\Filesystem       $filesystem     Filesystem
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack   Request stack
     * @param string                                         $filenameSuffix Filename suffix
     * @param string                                         $tmpDir         Temporary file directory
     * @param string                                         $uploadDir      Upload directory
     */
    public function __construct(Filesystem $filesystem, RequestStack $requestStack, string $filenameSuffix, string $tmpDir, string $uploadDir)
    {
        $this->filesystem = $filesystem;
        $this->requestStack = $requestStack;
        $this->filenameSuffix = $filenameSuffix;
        $this->tmpDir = $tmpDir;
        $this->uploadDir = $uploadDir;
    }

    /**
     * {@inheritDoc}
     */
    public function archive(): string
    {
        $this->prepareTmpDir();

        $filename = $this->buildFilename();
        $pathname = $this->buildPathname($filename);

        $this->buildZip($pathname);

        return $filename;
    }

    /**
     * {@inheritDoc}
     */
    public function buildPathname(string $filename): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            $this->tmpDir,
            $filename,
        ]);
    }

    /**
     * @param string $pathname Pathname
     *
     * @throws \RuntimeException
     */
    private function buildZip(string $pathname): void
    {
        $zip = new \ZipArchive();

        if (true !== $zip->open($pathname, \ZipArchive::CREATE)) {
            throw new \RuntimeException(sprintf('Unable to create image archive "%s".', $pathname));
        }
        try {
            $finder = (new Finder())->in($this->uploadDir);
        } catch (\InvalidArgumentException $ex) {
            throw new \RuntimeException(sprintf('Image upload directory "%s" does not exist.', $this->uploadDir));
        }
        /** @var \Symfony\Component\Finder\SplFileInfo $dir */
        foreach ($finder->directories() as $dir) {
            if (!$zip->addEmptyDir($dir->getRelativePathname())) {
                throw new \RuntimeException(
                    sprintf('Unable to create directory "%s" in image archive "%s".', $dir->getRelativePathname(), $pathname)
                );
            }
        }
        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder->files() as $file) {
            if (!$zip->addFile($file->getPathname(), $file->getRelativePathname())) {
                throw new \RuntimeException(
                    sprintf('Unable to add file "%s" to image archive "%s".', $file->getPathname(), $pathname)
                );
            }
        }
        if (!$zip->close()) {
            throw new \RuntimeException(sprintf('Unable to close image archive "%s".', $pathname));
        }
    }

    /**
     * @throws \RuntimeException
     */
    private function prepareTmpDir(): void
    {
        if (!$this->filesystem->exists($this->tmpDir)) {
            try {
                $this->filesystem->mkdir($this->tmpDir);
            } catch (IOException $ex) {
                throw new \RuntimeException(
                    sprintf('Unable to create image archive temporary file directory "%s": "%s".', $this->tmpDir, $ex->getMessage())
                );
            }
        }
        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ((new Finder())->in($this->tmpDir) as $file) {
            $this->filesystem->remove($file->getPathname());
        }
    }

    /**
     * @return string
     */
    private function buildFilename(): string
    {
        $parts = array_merge(preg_split('/[^0-9a-z]+/i', $this->getHost()), [
            $this->filenameSuffix,
            (new \DateTime())->format('Y-m-d_H-i'),
        ]);

        return sprintf('%s.zip', implode('_', $parts));
    }

    /**
     * @return string
     */
    private function getHost(): string
    {
        $request = $this->requestStack->getCurrentRequest();

        return null !== $request ? $request->getHost() : '';
    }
}
