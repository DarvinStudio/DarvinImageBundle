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
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * Cleanup Imagine cache event subscriber
 */
class CleanupImagineCacheSubscriber implements EventSubscriber
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
     * @param \Liip\ImagineBundle\Imagine\Cache\CacheManager   $imagineCacheManager  Imagine cache manager
     * @param \Liip\ImagineBundle\Imagine\Filter\FilterManager $imagineFilterManager Imagine filter manager
     * @param \Vich\UploaderBundle\Storage\StorageInterface    $uploaderStorage      Uploader storage
     */
    public function __construct(
        CacheManager $imagineCacheManager,
        FilterManager $imagineFilterManager,
        StorageInterface $uploaderStorage
    ) {
        $this->imagineCacheManager = $imagineCacheManager;
        $this->imagineFilterManager = $imagineFilterManager;
        $this->uploaderStorage = $uploaderStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
        ];
    }

    /**
     * @param \Doctrine\ORM\Event\OnFlushEventArgs $args Event arguments
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $filters = null;

        foreach ($args->getEntityManager()->getUnitOfWork()->getScheduledEntityDeletions() as $entity) {
            if (!$entity instanceof AbstractImage) {
                continue;
            }
            if (null === $filters) {
                $filters = array_keys($this->imagineFilterManager->getFilterConfiguration()->all());
            }

            $this->imagineCacheManager->remove(
                $this->uploaderStorage->resolveUri($entity, AbstractImage::PROPERTY_FILE),
                $filters
            );
        }
    }
}
