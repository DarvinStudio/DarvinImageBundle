<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\EventListener;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * Rename image event subscriber
 */
class RenameImageSubscriber implements EventSubscriber
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
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
        ];
    }

    /**
     * @param \Doctrine\ORM\Event\OnFlushEventArgs $args Event arguments
     */
    public function onFlush(OnFlushEventArgs $args): void
    {
        $uow = $args->getEntityManager()->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (!$entity instanceof AbstractImage) {
                continue;
            }

            $changeSet = $uow->getEntityChangeSet($entity);

            if (!isset($changeSet['name'])) {
                continue;
            }

            $pathname = $this->uploaderStorage->resolvePath($entity, AbstractImage::PROPERTY_FILE);
            $tmpPathname = $this->generateTmpPathname();

            $this->filesystem->rename($pathname, $tmpPathname, true);

            $filename = $entity->getName().'.'.$entity->getExtension();
            $entity->setFile(new UploadedFile($tmpPathname, $filename, null, null, null, true));
        }
    }

    /**
     * @return string
     *
     * @throws \RuntimeException
     */
    private function generateTmpPathname(): string
    {
        $pathname = tempnam($this->tmpDir, '');

        if (false === $pathname) {
            throw new \RuntimeException(sprintf('Unable to create temporary file for renamed image in "%s".', $this->tmpDir));
        }

        return $pathname;
    }
}
