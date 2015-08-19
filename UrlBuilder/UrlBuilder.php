<?php
/**
 * @author    Lev Semin <lev@darvin-studio.ru>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\UrlBuilder;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\ImageBundle\Size\Resolver\Pool\SizeResolverPoolInterface;
use Darvin\ImageBundle\UrlBuilder\Exception\FilterAlreadyExistsException;
use Darvin\ImageBundle\UrlBuilder\Exception\FilterNotFoundException;
use Darvin\ImageBundle\UrlBuilder\Exception\ImageNotFoundException;
use Darvin\ImageBundle\UrlBuilder\Exception\UrlBuilderException;
use Darvin\ImageBundle\UrlBuilder\Filter\FilterInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * URL builder
 */
class UrlBuilder implements UrlBuilderInterface
{
    const FILE_PROPERTY = 'file';

    /**
     * @var \Darvin\ImageBundle\Size\Resolver\Pool\SizeResolverPoolInterface
     */
    private $sizeResolverPool;

    /**
     * @var \Vich\UploaderBundle\Storage\StorageInterface
     */
    private $storage;

    /**
     * @var \Darvin\ImageBundle\UrlBuilder\Filter\FilterInterface[]
     */
    private $filters;

    /**
     * @param \Darvin\ImageBundle\Size\Resolver\Pool\SizeResolverPoolInterface $sizeResolverPool Image size resolver pool
     * @param \Vich\UploaderBundle\Storage\StorageInterface                    $storage          Storage
     */
    public function __construct(SizeResolverPoolInterface $sizeResolverPool, StorageInterface $storage)
    {
        $this->sizeResolverPool = $sizeResolverPool;
        $this->storage = $storage;
        $this->filters = array();
    }

    /**
     * {@inheritdoc}
     */
    public function buildUrlToOriginal(AbstractImage $image)
    {
        $this->checkIfFileExists($image);

        return $this->storage->resolveUri($image, self::FILE_PROPERTY, get_class($image));
    }

    /**
     * {@inheritdoc}
     */
    public function buildUrlToFilter(AbstractImage $image, $filterName, array $parameters = array(), $includeSizes = true)
    {
        $this->checkIfFileExists($image);

        $filter = $this->getFilter($filterName);

        if ($includeSizes && !isset($parameters['width']) && !isset($parameters['height'])) {
            if (!isset($parameters['size_name'])) {
                throw new UrlBuilderException(
                    'Parameter "size_name" must be provided in order to include sizes to filter.'
                );
            }

            $sizeResolver = $this->sizeResolverPool->getForObject($image);
            list($parameters['width'], $parameters['height']) = $sizeResolver->findSize($image, $parameters['size_name']);
        }

        return $filter->buildUrl($this->buildUrlToOriginal($image), $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function fileExists(AbstractImage $image = null)
    {
        return !empty($image) ? (bool) $this->getImagePathname($image) : false;
    }

    /**
     * @param \Darvin\ImageBundle\UrlBuilder\Filter\FilterInterface $filter Filter
     *
     * @throws \Darvin\ImageBundle\UrlBuilder\Exception\FilterAlreadyExistsException
     */
    public function addFilter(FilterInterface $filter)
    {
        if ($this->hasFilter($filter->getName())) {
            throw new FilterAlreadyExistsException($filter->getName());
        }

        $this->filters[$filter->getName()] = $filter;
    }

    /**
     * @param string $name Filter name
     *
     * @return \Darvin\ImageBundle\UrlBuilder\Filter\FilterInterface
     * @throws \Darvin\ImageBundle\UrlBuilder\Exception\FilterNotFoundException
     */
    private function getFilter($name)
    {
        if (!$this->hasFilter($name)) {
            throw new FilterNotFoundException($name);
        }

        return $this->filters[$name];
    }

    /**
     * @param string $name Filter name
     *
     * @return bool
     */
    private function hasFilter($name)
    {
        return isset($this->filters[$name]);
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image Image
     *
     * @throws \Darvin\ImageBundle\UrlBuilder\Exception\ImageNotFoundException
     */
    private function checkIfFileExists(AbstractImage $image)
    {
        if (!$this->fileExists($image)) {
            throw new ImageNotFoundException($this->getImagePathname($image));
        }
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image Image
     *
     * @return string
     */
    private function getImagePathname(AbstractImage $image = null)
    {
        return !empty($image) ? $this->storage->resolvePath($image, self::FILE_PROPERTY, get_class($image)) : null;
    }
}
