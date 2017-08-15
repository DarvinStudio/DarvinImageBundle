<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\EventListener;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\Utils\Event\CloneEvent;
use Darvin\Utils\EventListener\AbstractOnFlushListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * Image event listener
 */
class ImageListener extends AbstractOnFlushListener
{
    /**
     * @var \Liip\ImagineBundle\Imagine\Cache\CacheManager
     */
    private $imagineCacheManager;

    /**
     * @var \Liip\ImagineBundle\Imagine\Filter\FilterManager
     */
    private $imagineFilterManager;

    /**
     * @var \Vich\UploaderBundle\Storage\StorageInterface
     */
    private $uploaderStorage;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param \Liip\ImagineBundle\Imagine\Cache\CacheManager   $imagineCacheManager  Imagine cache manager
     * @param \Liip\ImagineBundle\Imagine\Filter\FilterManager $imagineFilterManager Imagine filter manager
     * @param \Vich\UploaderBundle\Storage\StorageInterface    $uploaderStorage      Uploader storage
     * @param string                                           $cacheDir             Cache directory
     */
    public function __construct(
        CacheManager $imagineCacheManager,
        FilterManager $imagineFilterManager,
        StorageInterface $uploaderStorage,
        $cacheDir
    ) {
        $this->imagineCacheManager = $imagineCacheManager;
        $this->imagineFilterManager = $imagineFilterManager;
        $this->uploaderStorage = $uploaderStorage;
        $this->cacheDir = $cacheDir;
    }

    /**
     * @param \Darvin\Utils\Event\CloneEvent $event Event
     */
    public function postClone(CloneEvent $event)
    {
        $original = $event->getOriginal();

        if (!$original instanceof AbstractImage) {
            return;
        }

        /** @var \Darvin\ImageBundle\Entity\Image\AbstractImage $clone */
        $clone = $event->getClone();

        $pathname = $this->uploaderStorage->resolvePath($original, AbstractImage::PROPERTY_FILE);
        $tmpPathname = $this->generateTmpPathname();

        $fs = new Filesystem();

        try {
            $fs->copy($pathname, $tmpPathname, true);
        } catch (FileNotFoundException $ex) {
            $event->setClone(null);

            return;
        }

        $file = new UploadedFile($tmpPathname, $original->getFilename(), null, null, null, true);

        $clone
            ->setFile($file)
            ->setName($original->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        parent::onFlush($args);

        $this
            ->onUpdate([$this, 'onImageUpdate'], AbstractImage::class)
            ->onDelete([$this, 'onImageDelete'], AbstractImage::class);
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image Updated image
     */
    protected function onImageUpdate(AbstractImage $image)
    {
        $changeSet = $this->uow->getEntityChangeSet($image);

        if (!isset($changeSet['name'])) {
            return;
        }

        $pathname = $this->uploaderStorage->resolvePath($image, AbstractImage::PROPERTY_FILE);
        $tmpPathname = $this->generateTmpPathname();

        $fs = new Filesystem();
        $fs->rename($pathname, $tmpPathname, true);

        $filename = $image->getName().'.'.$image->getExtension();
        $image->setFile(new UploadedFile($tmpPathname, $filename, null, null, null, true));
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image Deleted image
     */
    protected function onImageDelete(AbstractImage $image)
    {
        $this->imagineCacheManager->remove(
            $this->uploaderStorage->resolveUri($image, AbstractImage::PROPERTY_FILE),
            array_keys($this->imagineFilterManager->getFilterConfiguration()->all())
        );
    }

    /**
     * @return string
     */
    private function generateTmpPathname()
    {
        return tempnam($this->cacheDir, 'darvin_image_');
    }
}
