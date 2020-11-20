<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2018-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Size;

use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Image size describer
 */
class ImageSizeDescriber implements ImageSizeDescriberInterface
{
    /**
     * @var \Liip\ImagineBundle\Imagine\Filter\FilterConfiguration
     */
    private $filterConfig;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $entityConfig;

    /**
     * @param \Liip\ImagineBundle\Imagine\Filter\FilterConfiguration $filterConfig Imagine filter configuration
     * @param \Symfony\Contracts\Translation\TranslatorInterface     $translator   Translator
     * @param array                                                  $entityConfig Entity filter set configuration
     */
    public function __construct(FilterConfiguration $filterConfig, TranslatorInterface $translator, array $entityConfig)
    {
        $this->filterConfig = $filterConfig;
        $this->translator = $translator;
        $this->entityConfig = $entityConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function describeSize($filterSetNames = null, int $width = 0, int $height = 0, ?string $entityClass = null): ?string
    {
        if (null === $filterSetNames) {
            $filterSetNames = null !== $entityClass ? $this->getEntityFilterSets($entityClass) : [];
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
    private function getEntityFilterSets(string $class): array
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
    private function getMaxSize(array $filterSetNames): array
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
    private function getSize(string $filterSetName): array
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
