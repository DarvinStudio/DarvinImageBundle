<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.04.15
 * Time: 22:13
 */

namespace Darvin\ImageBundle\ImageCreator;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;

/**
 * Image creator
 */
class ImageCreator implements ImageCreatorInterface
{
    /**
     * @var \Liip\ImagineBundle\Imagine\Cache\CacheManager
     */
    private $cacheManager;

    /**
     * @var \Liip\ImagineBundle\Imagine\Filter\FilterManager
     */
    private $filterManager;

    /**
     * @var \Liip\ImagineBundle\Imagine\Data\DataManager
     */
    private $dataManager;

    /**
     * @var string
     */
    private $filterName;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $removeFromPath;

    /**
     * @param \Liip\ImagineBundle\Imagine\Cache\CacheManager   $cacheManager   Imagine cache manager
     * @param \Liip\ImagineBundle\Imagine\Filter\FilterManager $filterManager  Imagine filter manager
     * @param \Liip\ImagineBundle\Imagine\Data\DataManager     $dataManager    Imagine data manager
     * @param string                                           $filterName     Filter name
     * @param string                                           $secret         Secret
     * @param string                                           $removeFromPath Remove from path
     */
    public function __construct(
        CacheManager $cacheManager,
        FilterManager $filterManager,
        DataManager $dataManager,
        $filterName,
        $secret,
        $removeFromPath
    )
    {
        $this->cacheManager = $cacheManager;
        $this->filterManager = $filterManager;
        $this->dataManager = $dataManager;
        $this->filterName=$filterName;
        $this->secret = $secret;
        $this->removeFromPath = $removeFromPath;
    }

    /**
     * @param string $relativePathToImage Relative path to image
     * @param array  $filters             Filters
     *
     * @return string
     */
    public function createImage($relativePathToImage, array $filters = array())
    {
        $path = $this->createPath($relativePathToImage, $filters);

        if (!$this->cacheManager->isStored($path, $this->filterName)) {
            $binary = $this->dataManager->find($this->filterName, $relativePathToImage);
            $this->cacheManager->store(
                $this->filterManager->applyFilter($binary, $this->filterName, array(
                    'filters' => $filters,
                )),
                $path,
                $this->filterName
            );
        }

        return $this->cacheManager->resolve($path, $this->filterName);
    }

    /**
     * @param string $pathToImage Path to image
     * @param array  $filters     Filters
     *
     * @return string
     */
    private function createPath($pathToImage, array $filters)
    {
        $dir = dirname($pathToImage);
        $file = ltrim(str_replace($dir, '', $pathToImage), '/');
        $dir = str_replace($this->removeFromPath, '', $dir);

        array_walk_recursive($filters, function (&$value) {
            $value = (string) $value;
        });

        $sign = substr(preg_replace('/[^a-zA-Z0-9-_]/', '', base64_encode(hash_hmac('sha256', serialize($filters), $this->secret, true))), 0, 8);

        return sprintf('%s/%s/%s', $dir, $sign, $file);
    }
}
