<?php
/**
 * @author    Lev Semin     <lev@darvin-studio.ru>
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
     * @var \Liip\ImagineBundle\Imagine\Data\DataManager
     */
    private $dataManager;

    /**
     * @var \Liip\ImagineBundle\Imagine\Filter\FilterManager
     */
    private $filterManager;

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
    private $uploadPath;

    /**
     * @param \Liip\ImagineBundle\Imagine\Cache\CacheManager   $cacheManager  Imagine cache manager
     * @param \Liip\ImagineBundle\Imagine\Data\DataManager     $dataManager   Imagine data manager
     * @param \Liip\ImagineBundle\Imagine\Filter\FilterManager $filterManager Imagine filter manager
     * @param string                                           $filterName    Filter name
     * @param string                                           $secret        Secret
     * @param string                                           $uploadPath    Upload path
     */
    public function __construct(
        CacheManager $cacheManager,
        DataManager $dataManager,
        FilterManager $filterManager,
        $filterName,
        $secret,
        $uploadPath
    ) {
        $this->cacheManager = $cacheManager;
        $this->dataManager = $dataManager;
        $this->filterManager = $filterManager;
        $this->filterName = $filterName;
        $this->secret = $secret;
        $this->uploadPath = $uploadPath;
    }

    /**
     * {@inheritdoc}
     */
    public function createImage($imagePathname, array $filters = array())
    {
        $path = $this->createPath($imagePathname, $filters);

        if (!$this->cacheManager->isStored($path, $this->filterName)) {
            $binary = $this->dataManager->find($this->filterName, $imagePathname);

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
     * @param string $imagePathname Image pathname
     * @param array  $filters       Filters
     *
     * @return string
     */
    private function createPath($imagePathname, array $filters)
    {
        $dir = dirname($imagePathname);
        $filename = ltrim(str_replace($dir, '', $imagePathname), '/');
        $dir = str_replace($this->uploadPath, '', $dir);

        array_walk_recursive($filters, function (&$value) {
            $value = (string) $value;
        });

        $sign = substr(
            preg_replace('/[^a-zA-Z0-9-_]/', '', base64_encode(hash_hmac('sha256', serialize($filters), $this->secret, true))),
            0,
            8
        );

        return sprintf('%s/%s/%s', $dir, $sign, $filename);
    }
}
