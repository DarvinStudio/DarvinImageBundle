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

namespace Darvin\ImageBundle\UrlBuilder\Filter;

use Darvin\ImageBundle\ImageCreator\ImageCreatorInterface;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;

/**
 * URL builder resize filter
 */
class ResizeFilter implements FilterInterface
{
    const NAME = 'darvin_resize';

    /**
     * @var \Liip\ImagineBundle\Imagine\Filter\FilterManager
     */
    private $filterManager;

    /**
     * @var \Darvin\ImageBundle\ImageCreator\ImageCreatorInterface
     */
    private $imageCreator;

    /**
     * @param \Liip\ImagineBundle\Imagine\Filter\FilterManager       $filterManager Imagine filter manager
     * @param \Darvin\ImageBundle\ImageCreator\ImageCreatorInterface $imageCreator  Image creator
     */
    public function __construct(FilterManager $filterManager, ImageCreatorInterface $imageCreator)
    {
        $this->filterManager = $filterManager;
        $this->imageCreator = $imageCreator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildUrl($imagePathname, array $parameters)
    {
        $options = array(
            'size' => $this->getSize($parameters),
            'mode' => isset($parameters['outbound']) && $parameters['outbound'] ? 'outbound' : 'inset',
        );
        $filters = array(
            'thumbnail' => $options,
        );

        if (isset($parameters['watermark'])) {
            $filters['watermark'] = $this->getWatermarkConfiguration($parameters['watermark']);
        }

        return $this->imageCreator->createImage($imagePathname, $filters);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @param array $parameters Parameters
     *
     * @return array
     * @throws \Darvin\ImageBundle\UrlBuilder\Filter\FilterException
     */
    private function getSize(array $parameters)
    {
        $width = isset($parameters['width']) ? $parameters['width'] : null;
        $height = isset($parameters['height']) ? $parameters['height'] : null;

        if (null === $width && null === $height) {
            throw new FilterException('Width or height must be provided.');
        }

        return array($width, $height);
    }

    /**
     * @param string $name Filter name
     *
     * @return array
     * @throws \Darvin\ImageBundle\UrlBuilder\Filter\FilterException
     * @throws \Darvin\ImageBundle\UrlBuilder\Exception\FilterNotFoundException
     */
    private function getWatermarkConfiguration($name)
    {
        $filterConfiguration = $this->filterManager->getFilterConfiguration()->get($name);

        if (!isset($filterConfiguration['filters']['watermark'])) {
            throw new FilterException(sprintf('Filter "%s" does not contain watermark configuration.', $name));
        }

        return $filterConfiguration['filters']['watermark'];
    }
}
