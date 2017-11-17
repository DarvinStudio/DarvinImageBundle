<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\EventListener;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\Utils\Event\CloneEvent;
use Darvin\Utils\Event\Events;
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
    private $cacheDir;

    /**
     * @param \Symfony\Component\Filesystem\Filesystem      $filesystem      Filesystem
     * @param \Vich\UploaderBundle\Storage\StorageInterface $uploaderStorage Uploader storage
     * @param string                                        $cacheDir        Cache directory
     */
    public function __construct(Filesystem $filesystem, StorageInterface $uploaderStorage, $cacheDir)
    {
        $this->filesystem = $filesystem;
        $this->uploaderStorage = $uploaderStorage;
        $this->cacheDir = $cacheDir;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::POST_CLONE => 'copyFile',
        ];
    }

    /**
     * @param \Darvin\Utils\Event\CloneEvent $event Event
     */
    public function copyFile(CloneEvent $event)
    {
        $original = $event->getOriginal();

        if (!$original instanceof AbstractImage) {
            return;
        }

        /** @var \Darvin\ImageBundle\Entity\Image\AbstractImage $clone */
        $clone = $event->getClone();

        $pathname = $this->uploaderStorage->resolvePath($original, AbstractImage::PROPERTY_FILE);
        $tmpPathname = $this->generateTmpPathname();

        try {
            $this->filesystem->copy($pathname, $tmpPathname, true);
        } catch (FileNotFoundException $ex) {
            $event->setClone(null);

            return;
        }

        $clone
            ->setFile(new UploadedFile($tmpPathname, $original->getFilename(), null, null, null, true))
            ->setName(null);
    }

    /**
     * @return string
     */
    private function generateTmpPathname()
    {
        return tempnam($this->cacheDir, 'darvin_image_');
    }
}
