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
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * Image event listener
 */
class ImageListener
{
    /**
     * @var \Vich\UploaderBundle\Storage\StorageInterface
     */
    private $storage;

    /**
     * @param \Vich\UploaderBundle\Storage\StorageInterface $storage Storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param \Vich\UploaderBundle\Event\Event $event Event
     */
    public function postUpload(Event $event)
    {
        $image = $event->getObject();

        if (!$image instanceof AbstractImage) {
            return;
        }

        $image
            ->setExtension(pathinfo($image->getFilename(), PATHINFO_EXTENSION))
            ->setName(preg_replace(sprintf('/\.%s$/', $image->getExtension()), '', $image->getFilename()));
    }

    /**
     * @param \Doctrine\ORM\Event\OnFlushEventArgs $args Event arguments
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $uow = $args->getEntityManager()->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof AbstractImage) {
                $this->onImageUpdate($entity, $uow);
            }
        }
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image Updated image
     * @param \Doctrine\ORM\UnitOfWork                       $uow   Unit of work
     */
    private function onImageUpdate(AbstractImage $image, UnitOfWork $uow)
    {
        $changeSet = $uow->getEntityChangeSet($image);

        if (!isset($changeSet['name'])) {
            return;
        }

        $pathname = $this->storage->resolvePath($image, AbstractImage::PROPERTY_FILE);
        $tmpPathname = tempnam(sys_get_temp_dir(), 'darvin_image_');

        $fs = new Filesystem();
        $fs->rename($pathname, $tmpPathname, true);

        $filename = $image->getName().'.'.$image->getExtension();
        $image->setFile(new UploadedFile($tmpPathname, $filename, null, null, null, true));
    }
}
