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
use Doctrine\ORM\EntityManager;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * Image post upload event listener
 */
class ImagePostUploadListener
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Liip\ImagineBundle\Imagine\Cache\CacheManager
     */
    private $imagineCacheManager;

    /**
     * @var \Liip\ImagineBundle\Imagine\Data\DataManager
     */
    private $imagineDataManager;

    /**
     * @var \Liip\ImagineBundle\Imagine\Filter\FilterManager
     */
    private $imagineFilterManager;

    /**
     * @var \Vich\UploaderBundle\Storage\StorageInterface
     */
    private $uploaderStorage;

    /**
     * @param \Doctrine\ORM\EntityManager                      $em                   Entity manager
     * @param \Liip\ImagineBundle\Imagine\Cache\CacheManager   $imagineCacheManager  Imagine cache manager
     * @param \Liip\ImagineBundle\Imagine\Data\DataManager     $imagineDataManager   Imagine data manager
     * @param \Liip\ImagineBundle\Imagine\Filter\FilterManager $imagineFilterManager Imagine filter manager
     * @param \Vich\UploaderBundle\Storage\StorageInterface    $uploaderStorage      Uploader storage
     */
    public function __construct(
        EntityManager $em,
        CacheManager $imagineCacheManager,
        DataManager $imagineDataManager,
        FilterManager $imagineFilterManager,
        StorageInterface $uploaderStorage
    ) {
        $this->em = $em;
        $this->imagineCacheManager = $imagineCacheManager;
        $this->imagineDataManager = $imagineDataManager;
        $this->imagineFilterManager = $imagineFilterManager;
        $this->uploaderStorage = $uploaderStorage;
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

        $image->setExtension(pathinfo($image->getFilename(), PATHINFO_EXTENSION));

        if (null === $image->getName()) {
            $image->setName(preg_replace(sprintf('/\.%s$/', $image->getExtension()), '', $image->getFilename()));
        }
        // Update cached images
        if (null !== $image->getId() && null !== $image->getFile()) {
            $filters = array_keys($this->imagineFilterManager->getFilterConfiguration()->all());

            // Remove old cached images if filename changed
            $changeSet = $this->em->getUnitOfWork()->getEntityChangeSet($image);

            if (isset($changeSet['filename']) && !empty($changeSet['filename'][0])) {
                $mapping = $event->getMapping();
                $uploadDir = $mapping->getUploadDir($image);
                $uploadDir = !empty($uploadDir) ? str_replace('\\', '/', $uploadDir).'/' : '';
                $this->imagineCacheManager->remove($mapping->getUriPrefix().'/'.$uploadDir.$changeSet['filename'][0], $filters);
            }

            // Create new cached images
            $path = $this->uploaderStorage->resolveUri($image, AbstractImage::PROPERTY_FILE);

            foreach ($filters as $filter) {
                $this->imagineCacheManager->store(
                    $this->imagineFilterManager->applyFilter($this->imagineDataManager->find($filter, $path), $filter),
                    $path,
                    $filter
                );
            }
        }
    }
}
