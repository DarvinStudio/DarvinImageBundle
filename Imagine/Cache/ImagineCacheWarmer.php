<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Imagine\Cache;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Doctrine\ORM\EntityManager;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * Imagine cache warmer
 */
class ImagineCacheWarmer
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
     * @var \Vich\UploaderBundle\Mapping\PropertyMappingFactory
     */
    private $uploaderMappingFactory;

    /**
     * @var \Vich\UploaderBundle\Storage\StorageInterface
     */
    private $uploaderStorage;

    /**
     * @var array
     */
    private $imagineFilterSets;

    /**
     * @param \Doctrine\ORM\EntityManager                         $em                     Entity manager
     * @param \Liip\ImagineBundle\Imagine\Cache\CacheManager      $imagineCacheManager    Imagine cache manager
     * @param \Liip\ImagineBundle\Imagine\Data\DataManager        $imagineDataManager     Imagine data manager
     * @param \Liip\ImagineBundle\Imagine\Filter\FilterManager    $imagineFilterManager   Imagine filter manager
     * @param \Vich\UploaderBundle\Mapping\PropertyMappingFactory $uploaderMappingFactory Uploader property mapping factory
     * @param \Vich\UploaderBundle\Storage\StorageInterface       $uploaderStorage        Uploader storage
     * @param array                                               $imagineFilterSets      Imagine filter sets
     */
    public function __construct(
        EntityManager $em,
        CacheManager $imagineCacheManager,
        DataManager $imagineDataManager,
        FilterManager $imagineFilterManager,
        PropertyMappingFactory $uploaderMappingFactory,
        StorageInterface $uploaderStorage,
        array $imagineFilterSets
    ) {
        $this->em = $em;
        $this->imagineCacheManager = $imagineCacheManager;
        $this->imagineDataManager = $imagineDataManager;
        $this->imagineFilterManager = $imagineFilterManager;
        $this->uploaderMappingFactory = $uploaderMappingFactory;
        $this->uploaderStorage = $uploaderStorage;
        $this->imagineFilterSets = $imagineFilterSets;
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image Image
     */
    public function warmupImageCache(AbstractImage $image)
    {
        $filters = array_keys($this->imagineFilterManager->getFilterConfiguration()->all());
        $filters = array_combine($filters, $filters);

        foreach ($this->imagineFilterSets as $name => $filterSet) {
            unset($filters[$name]);

            if (!isset($filterSet['entities'])) {
                continue;
            }
            foreach ((array)$filterSet['entities'] as $entity) {
                if ($image instanceof $entity) {
                    $filters[] = $name;
                }
            }
        }
        if (empty($filters)) {
            return;
        }

        $filters = array_values($filters);

        // Remove old cached images if filename changed
        $changeSet = $this->em->getUnitOfWork()->getEntityChangeSet($image);

        if (isset($changeSet['filename']) && !empty($changeSet['filename'][0])) {
            $mapping = $this->uploaderMappingFactory->fromField($image, AbstractImage::PROPERTY_FILE, AbstractImage::class);
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
