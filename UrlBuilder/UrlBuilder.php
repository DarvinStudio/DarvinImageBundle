<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.04.15
 * Time: 12:24
 */

namespace Darvin\ImageBundle\UrlBuilder;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\ImageBundle\Size\Resolver\Pool\SizeResolverPoolInterface;
use Darvin\ImageBundle\UrlBuilder\Exception\FilterAlreadyExistsException;
use Darvin\ImageBundle\UrlBuilder\Exception\FilterNotFoundException;
use Darvin\ImageBundle\UrlBuilder\Exception\ImageNotFoundException;
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
    public function hasFile(AbstractImage $image = null)
    {
        return !empty($image) ? (bool) $this->getPathToFile($image) : false;
    }

    /**
     * {@inheritdoc}
     */
    public function buildUrlToOriginal(AbstractImage $image)
    {
        $this->checkFile($image);

        return $this->storage->resolveUri($image, self::FILE_PROPERTY, get_class($image));
    }

    /**
     * {@inheritdoc}
     */
    public function buildUrlToFilter(AbstractImage $image, $filterName, array $parameters = array(), $includeSizes = true)
    {
        $this->checkFile($image);
        $filter = $this->getFilter($filterName);

        if ($includeSizes && !isset($parameters['width']) && !isset($parameters['height'])) {
            if (!isset($parameters['size_name'])) {
                throw new \InvalidArgumentException(
                    'Parameter "size_name" must be provided in order to include sizes to filter.'
                );
            }

            $sizeResolver = $this->sizeResolverPool->getForObject($image);
            list($parameters['width'], $parameters['height']) = $sizeResolver->findSize($image, $parameters['size_name']);
        }

        return $filter->buildUrl($this->buildUrlToOriginal($image), $parameters);
    }

    /**
     * @param string                                                $name   Filter name
     * @param \Darvin\ImageBundle\UrlBuilder\Filter\FilterInterface $filter Filter
     *
     * @throws \Darvin\ImageBundle\UrlBuilder\Exception\FilterAlreadyExistsException
     */
    public function addFilter($name, FilterInterface $filter)
    {
        if ($this->hasFilter($name)) {
            throw new FilterAlreadyExistsException($name);
        }

        $this->filters[$name] = $filter;
    }

    /**
     * @param string $name Filter name
     *
     * @return bool
     */
    public function hasFilter($name)
    {
        return isset($this->filters[$name]);
    }

    /**
     * @param string $name Filter name
     *
     * @return \Darvin\ImageBundle\UrlBuilder\Filter\FilterInterface
     * @throws \Darvin\ImageBundle\UrlBuilder\Exception\FilterNotFoundException
     */
    public function getFilter($name)
    {
        if (!$this->hasFilter($name)) {
            throw new FilterNotFoundException($name);
        }

        return $this->filters[$name];
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image Image
     *
     * @throws \Darvin\ImageBundle\UrlBuilder\Exception\ImageNotFoundException
     */
    private function checkFile(AbstractImage $image)
    {
        if (!$this->hasFile($image)) {
            throw new ImageNotFoundException($this->getPathToFile($image));
        }
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image Image
     *
     * @return string
     */
    private function getPathToFile(AbstractImage $image = null)
    {
        return !empty($image) ? $this->storage->resolvePath($image, self::FILE_PROPERTY, get_class($image)) : null;
    }
}
