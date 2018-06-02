<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2018, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Size;

use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Image size describer
 */
class SizeDescriber
{
    /**
     * @var \Liip\ImagineBundle\Imagine\Filter\FilterConfiguration
     */
    private $filterConfig;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $entityConfig;

    /**
     * @param \Liip\ImagineBundle\Imagine\Filter\FilterConfiguration $filterConfig Imagine filter configuration
     * @param \Symfony\Component\Translation\TranslatorInterface     $translator   Translator
     * @param array                                                  $entityConfig Entity filter set configuration
     */
    public function __construct(FilterConfiguration $filterConfig, TranslatorInterface $translator, array $entityConfig)
    {
        $this->filterConfig = $filterConfig;
        $this->translator = $translator;
        $this->entityConfig = $entityConfig;
    }

    /**
     * @param string|string[]|null $filterSetNames Imagine filter set names
     * @param int                  $width          Width
     * @param int                  $height         Height
     * @param string               $entityClass    Image entity class
     *
     * @return string|null
     */
    public function describeSize($filterSetNames = null, $width = 0, $height = 0, $entityClass = null)
    {
        if (null === $filterSetNames) {
            $filterSetNames = !empty($entityClass) ? $this->getEntityFilterSets($entityClass) : [];
        }
        if (!is_array($filterSetNames)) {
            $filterSetNames = (array)$filterSetNames;
        }

        list($descriptionWidth, $descriptionHeight) = $this->getMaxSize($filterSetNames);

        if ($width > 0) {
            $descriptionWidth = $width;
        }
        if ($height > 0) {
            $descriptionHeight = $height;
        }
        if ($descriptionWidth > 0 && $descriptionHeight > 0) {
            return $this->translator->trans('size.description', [
                '%width%'  => $descriptionWidth,
                '%height%' => $descriptionHeight,
            ], 'darvin_image');
        }

        return null;
    }

    /**
     * @param string $class Image entity class
     *
     * @return string[]
     */
    private function getEntityFilterSets($class)
    {
        $filterSetNames = [];

        foreach ($this->entityConfig as $filterSetName => $params) {
            if (isset($params['entities']) && in_array($class, $params['entities'])) {
                $filterSetNames[] = $filterSetName;
            }
        }

        return $filterSetNames;
    }

    /**
     * @param string[] $filterSetNames Imagine filter set names
     *
     * @return int[]
     */
    private function getMaxSize(array $filterSetNames)
    {
        $maxWidth = $maxHeight = 0;

        foreach ($filterSetNames as $filterSetName) {
            list($width, $height) = $this->getSize($filterSetName);

            if ($width > $maxWidth) {
                $maxWidth = $width;
            }
            if ($height > $maxHeight) {
                $maxHeight = $height;
            }
        }

        return [$maxWidth, $maxHeight];
    }

    /**
     * @param string $filterSetName Imagine filter set name
     *
     * @return int[]
     */
    private function getSize($filterSetName)
    {
        $filterSet = $this->filterConfig->get($filterSetName);

        if (isset($filterSet['filters'])) {
            foreach ($filterSet['filters'] as $filterName => $params) {
                if ('thumbnail' === $filterName) {
                    return $params['size'];
                }
            }
        }

        return [0, 0];
    }
}
