<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\EventListener;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\Utils\Event\ClonableEvents;
use Darvin\Utils\Event\CloneEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * Copy cloned image file event subscriber
 */
class CopyClonedImageFileSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \Vich\UploaderBundle\Storage\StorageInterface
     */
    private $uploaderStorage;

    /**
     * @var string
     */
    private $tmpDir;

    /**
     * @param \Symfony\Component\Filesystem\Filesystem      $filesystem      Filesystem
     * @param \Vich\UploaderBundle\Storage\StorageInterface $uploaderStorage Uploader storage
     * @param string                                        $tmpDir          Temporary file directory
     */
    public function __construct(Filesystem $filesystem, StorageInterface $uploaderStorage, string $tmpDir)
    {
        $this->filesystem = $filesystem;
        $this->uploaderStorage = $uploaderStorage;
        $this->tmpDir = $tmpDir;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ClonableEvents::CLONED => 'copyFile',
        ];
    }

    /**
     * @param \Darvin\Utils\Event\CloneEvent $event Event
     */
    public function copyFile(CloneEvent $event): void
    {
        $original = $event->getOriginal();

        if (!$original instanceof AbstractImage) {
            return;
        }

        /** @var \Darvin\ImageBundle\Entity\Image\AbstractImage $clone */
        $clone       = $event->getClone();
        $pathname    = $this->uploaderStorage->resolvePath($original, AbstractImage::PROPERTY_FILE);
        $tmpPathname = $this->generateTmpPathname();

        try {
            $this->filesystem->copy($pathname, $tmpPathname, true);
        } catch (FileNotFoundException $ex) {
            $event->setClone(null);

            return;
        }

        $clone
            ->setFile(new UploadedFile($tmpPathname, $original->getFilename(), null, null, true))
            ->setName(null);
    }

    /**
     * @return string
     *
     * @throws \RuntimeException
     */
    private function generateTmpPathname(): string
    {
        $pathname = @tempnam($this->tmpDir, '');

        if (false === $pathname) {
            throw new \RuntimeException(sprintf('Unable to create temporary file for cloned image in "%s".', $this->tmpDir));
        }

        return $pathname;
    }
}
