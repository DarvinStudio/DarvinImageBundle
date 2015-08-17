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
 * Resize filter
 */
class ResizeFilter implements FilterInterface
{
    const FILTER_CROP   = 'crop';
    const FILTER_RESIZE = 'thumbnail';

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
     * @param string $pathToImage Path to image
     * @param array  $parameters  Parameters
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function buildUrl($pathToImage, array $parameters)
    {
        $type = isset($parameters['type']) ? $parameters['type'] : $this->getDefaultFilter();

        if (!in_array($type, $this->getAllowedFilters())) {
            throw new \InvalidArgumentException(
                sprintf('Parameter type must be one of this: "%s".', implode(', ', $this->getAllowedFilters()))
            );
        }

        $width = isset($parameters['width']) ? $parameters['width'] : null;
        $height = isset($parameters['height']) ? $parameters['height'] : null;

        if (!$width && !$height) {
            throw new \InvalidArgumentException('Width or height must be defined.');
        }

        $options = array(
            'size' => array($width, $height),
            'mode' => $type == self::FILTER_RESIZE ? 'inset' : 'outbound',
        );
        $filters = array(
            'thumbnail' => $options,
        );

        if (isset($parameters['watermark'])) {
            $watermarkOptions = $this->filterManager->getFilterConfiguration()->get($parameters['watermark']);

            if (!isset($watermarkOptions['filters']) || !isset($watermarkOptions['filters']['watermark'])) {
                throw new \InvalidArgumentException('Invalid watermark filter parameters.');
            }

            $filters['watermark'] = $watermarkOptions['filters']['watermark'];
        }

        return $this->imageCreator->createImage($pathToImage, $filters);
    }

    /**
     * @return array
     */
    private function getAllowedFilters()
    {
        return array(self::FILTER_CROP, self::FILTER_RESIZE);
    }

    /**
     * @return string
     */
    private function getDefaultFilter()
    {
        return self::FILTER_RESIZE;
    }
}
