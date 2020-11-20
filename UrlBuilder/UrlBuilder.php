<?php declare(strict_types=1);
/**
 * @author    Lev Semin     <lev@darvin-studio.ru>
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\UrlBuilder;

use Darvin\FileBundle\UrlBuilder\UrlAbsolutizerInterface;
use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\ImageBundle\UrlBuilder\Exception\FilterAlreadyExistsException;
use Darvin\ImageBundle\UrlBuilder\Exception\FilterNotFoundException;
use Darvin\ImageBundle\UrlBuilder\Filter\FilterInterface;

/**
 * URL builder
 */
class UrlBuilder implements UrlBuilderInterface
{
    /**
     * @var \Darvin\FileBundle\UrlBuilder\UrlBuilderInterface
     */
    private $genericUrlBuilder;

    /**
     * @var \Darvin\FileBundle\UrlBuilder\UrlAbsolutizerInterface
     */
    private $urlAbsolutizer;

    /**
     * @var string|null
     */
    private $placeholder;

    /**
     * @var \Darvin\ImageBundle\UrlBuilder\Filter\FilterInterface[]
     */
    private $filters;

    /**
     * @param \Darvin\FileBundle\UrlBuilder\UrlBuilderInterface     $genericUrlBuilder Generic URL builder
     * @param \Darvin\FileBundle\UrlBuilder\UrlAbsolutizerInterface $urlAbsolutizer    URL absolutizer
     * @param string|null                                           $placeholder       Placeholder image pathname relative to the web directory
     */
    public function __construct(
        \Darvin\FileBundle\UrlBuilder\UrlBuilderInterface $genericUrlBuilder,
        UrlAbsolutizerInterface $urlAbsolutizer,
        ?string $placeholder = null
    ) {
        $this->genericUrlBuilder = $genericUrlBuilder;
        $this->urlAbsolutizer = $urlAbsolutizer;
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

        return $this->urlAbsolutizer->absolutizeUrl(null !== $fallback ? $fallback : $this->placeholder);
    }

    /**
     * {@inheritDoc}
     */
    public function buildOriginalUrl(?AbstractImage $image, bool $prependHost = true, ?string $fallback = null): ?string
    {
        return $this->genericUrlBuilder->buildOriginalUrl($image, $prependHost, null !== $fallback ? $fallback : $this->placeholder);
    }

    /**
     * {@inheritDoc}
     */
    public function isActive(?AbstractImage $image): bool
    {
        return $this->genericUrlBuilder->isActive($image);
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
}
