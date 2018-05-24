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

namespace Darvin\ImageBundle\UrlBuilder;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\ImageBundle\UrlBuilder\Exception\FilterAlreadyExistsException;
use Darvin\ImageBundle\UrlBuilder\Exception\FilterNotFoundException;
use Darvin\ImageBundle\UrlBuilder\Exception\ImageNotFoundException;
use Darvin\ImageBundle\UrlBuilder\Exception\UrlBuilderException;
use Darvin\ImageBundle\UrlBuilder\Filter\FilterInterface;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\HttpFoundation\RequestStack;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * URL builder
 */
class UrlBuilder implements UrlBuilderInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Vich\UploaderBundle\Storage\StorageInterface
     */
    private $storage;

    /**
     * @var string|null
     */
    private $placeholder;

    /**
     * @var bool
     */
    private $placeholderIsVector;

    /**
     * @var \Darvin\ImageBundle\UrlBuilder\Filter\FilterInterface[]
     */
    private $filters;

    /**
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack Request stack
     * @param \Vich\UploaderBundle\Storage\StorageInterface  $storage      Storage
     * @param string|null                                    $placeholder  Placeholder image pathname relative to the web directory
     */
    public function __construct(RequestStack $requestStack, StorageInterface $storage, $placeholder)
    {
        $this->requestStack = $requestStack;
        $this->storage = $storage;
        $this->placeholder = $placeholder;

        $this->placeholderIsVector = !empty($placeholder) && preg_match('/\.svg$/', $placeholder);
        $this->filters = [];
    }

    /**
     * {@inheritdoc}
     */
    public function buildUrlToOriginal(AbstractImage $image = null, $addHost = false)
    {
        $this->checkIfFileExists($image);

        $url = !empty($image)
            ? $this->storage->resolveUri($image, AbstractImage::PROPERTY_FILE, ClassUtils::getClass($image))
            : '/'.$this->placeholder;

        if (!$addHost) {
            return $url;
        }

        $request = $this->requestStack->getCurrentRequest();

        if (empty($request)) {
            throw new UrlBuilderException('Unable to add host to URL: current request is empty.');
        }

        return $request->getSchemeAndHttpHost().$url;
    }

    /**
     * {@inheritdoc}
     */
    public function buildUrlToFilter(AbstractImage $image = null, $filterName, array $parameters = [])
    {
        $this->checkIfFileExists($image);

        $filter = $this->getFilter($filterName);

        if (!empty($image)) {
            return $filter->buildUrl($this->buildUrlToOriginal($image), $parameters);
        }
        if (!$this->placeholderIsVector) {
            return $filter->buildUrl($this->placeholder, $parameters);
        }

        $prefix = '/';

        $request = $this->requestStack->getCurrentRequest();

        if (!empty($request)) {
            $prefix = $request->getSchemeAndHttpHost().$prefix;
        }

        return $prefix.$this->placeholder;
    }

    /**
     * {@inheritdoc}
     */
    public function fileExists(AbstractImage $image = null)
    {
        if (empty($image)) {
            return !empty($this->placeholder);
        }

        return (bool)$this->getImagePathname($image);
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
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage|null $image Image
     *
     * @throws \Darvin\ImageBundle\UrlBuilder\Exception\ImageNotFoundException
     */
    private function checkIfFileExists(AbstractImage $image = null)
    {
        if (!$this->fileExists($image)) {
            throw new ImageNotFoundException(!empty($image) ? $this->getImagePathname($image) : null);
        }
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage $image Image
     *
     * @return string
     */
    private function getImagePathname(AbstractImage $image)
    {
        return $this->storage->resolvePath($image, AbstractImage::PROPERTY_FILE, ClassUtils::getClass($image));
    }
}
