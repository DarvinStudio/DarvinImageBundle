<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.04.15
 * Time: 14:28
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
