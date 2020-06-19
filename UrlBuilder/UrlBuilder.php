<?php declare(strict_types=1);
/**
 * @author    Lev Semin     <lev@darvin-studio.ru>
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\UrlBuilder;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\ImageBundle\UrlBuilder\Exception\FilterAlreadyExistsException;
use Darvin\ImageBundle\UrlBuilder\Exception\FilterNotFoundException;
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
     * @var \Darvin\ImageBundle\UrlBuilder\Filter\FilterInterface[]
     */
    private $filters;

    /**
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack Request stack
     * @param \Vich\UploaderBundle\Storage\StorageInterface  $storage      Storage
     * @param string|null                                    $placeholder  Placeholder image pathname relative to the web directory
     */
    public function __construct(RequestStack $requestStack, StorageInterface $storage, ?string $placeholder = null)
    {
        $this->requestStack = $requestStack;
        $this->storage = $storage;
        $this->placeholder = $placeholder;

        $this->filters = [];
    }

    /**
     * {@inheritDoc}
     */
    public function buildFilteredUrl(?AbstractImage $image, string $filterName, array $parameters = [], ?string $fallback = null): ?string
    {
        if (null !== $image && $image->isVector()) {
            return $this->buildOriginalUrl($image, true, $fallback);
        }
        if ($this->isActive($image)) {
            return $this->getFilter($filterName)->buildUrl($this->buildOriginalUrl($image, false), $parameters);
        }

        return $this->makeUrlAbsolute(null !== $fallback ? $fallback : $this->placeholder);
    }

    /**
     * {@inheritDoc}
     */
    public function buildOriginalUrl(?AbstractImage $image, bool $prependHost = true, ?string $fallback = null): ?string
    {
        if ($this->isActive($image)) {
            return $this->makeUrlAbsolute($this->storage->resolveUri($image, AbstractImage::PROPERTY_FILE, ClassUtils::getClass($image)), $prependHost);
        }

        return $this->makeUrlAbsolute(null !== $fallback ? $fallback : $this->placeholder, $prependHost);
    }

    /**
     * {@inheritDoc}
     */
    public function isActive(?AbstractImage $image): bool
    {
        return null !== $image && $image->isEnabled();
    }

    /**
     * @param \Darvin\ImageBundle\UrlBuilder\Filter\FilterInterface $filter Filter
     *
     * @throws \Darvin\ImageBundle\UrlBuilder\Exception\FilterAlreadyExistsException
     */
    public function addFilter(FilterInterface $filter): void
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
    private function getFilter(string $name): FilterInterface
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
    private function hasFilter(string $name): bool
    {
        return isset($this->filters[$name]);
    }

    /**
     * @param string|null $url         URL
     * @param bool        $prependHost Whether to prepend host
     *
     * @return string|null
     */
    private function makeUrlAbsolute(?string $url, bool $prependHost = true): ?string
    {
        if (null === $url) {
            return null;
        }

        $parts = ['/', ltrim($url, '/')];

        if ($prependHost) {
            $request = $this->requestStack->getCurrentRequest();

            if (null !== $request) {
                array_unshift($parts, $request->getSchemeAndHttpHost());
            }
        }

        return implode('', $parts);
    }
}
