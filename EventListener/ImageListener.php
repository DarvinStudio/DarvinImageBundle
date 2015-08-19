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
use Symfony\Component\Filesystem\Filesystem;
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
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Doctrine\ORM\UnitOfWork
     */
    private $uow;

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
        $this->em = $args->getEntityManager();
        $this->uow = $uow = $this->em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof AbstractImage) {
                $this->onImageUpdate($entity);
            }
        }
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image Updated image
     */
    private function onImageUpdate(AbstractImage $image)
    {
        $changeSet = $this->uow->getEntityChangeSet($image);

        if (!isset($changeSet['name'])) {
            return;
        }

        $pathname = $this->storage->resolvePath($image, AbstractImage::PROPERTY_FILE);

        $image->setFilename($image->getName().'.'.$image->getExtension());

        $fs = new Filesystem();
        $fs->rename($pathname, dirname($pathname).DIRECTORY_SEPARATOR.$image->getFilename());

        $this->uow->recomputeSingleEntityChangeSet($this->em->getClassMetadata(AbstractImage::CLASS_NAME), $image);
    }
}
