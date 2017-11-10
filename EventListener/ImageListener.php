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
use Darvin\Utils\EventListener\AbstractOnFlushListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * Image event listener
 */
class ImageListener extends AbstractOnFlushListener
{
    /**
     * @var \Vich\UploaderBundle\Storage\StorageInterface
     */
    private $uploaderStorage;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param \Vich\UploaderBundle\Storage\StorageInterface $uploaderStorage Uploader storage
     * @param string                                        $cacheDir        Cache directory
     */
    public function __construct(StorageInterface $uploaderStorage, $cacheDir)
    {
        $this->uploaderStorage = $uploaderStorage;
        $this->cacheDir = $cacheDir;
    }

    /**
     * {@inheritdoc}
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        parent::onFlush($args);

        $this->onUpdate([$this, 'onImageUpdate'], AbstractImage::class);
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
     * @return string
     */
    private function generateTmpPathname()
    {
        return tempnam($this->cacheDir, 'darvin_image_');
    }
}
