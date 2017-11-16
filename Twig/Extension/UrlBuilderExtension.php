<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Twig\Extension;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\ImageBundle\UrlBuilder\Filter\DirectImagineFilter;
use Darvin\ImageBundle\UrlBuilder\UrlBuilderInterface;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Psr\Log\LoggerInterface;

/**
 * URL builder Twig extension
 */
class UrlBuilderExtension extends \Twig_Extension
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Darvin\ImageBundle\UrlBuilder\UrlBuilderInterface
     */
    private $urlBuilder;

    /**
     * @param \Psr\Log\LoggerInterface                           $logger     Logger
     * @param \Darvin\ImageBundle\UrlBuilder\UrlBuilderInterface $urlBuilder URL builder
     */
    public function __construct(LoggerInterface $logger, UrlBuilderInterface $urlBuilder)
    {
        $this->logger = $logger;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('image_filter', [$this, 'buildImagine']),
            new \Twig_SimpleFilter('image_original', [$this, 'buildUrlToOriginal']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('image_exists', [$this->urlBuilder, 'fileExists']),
        ];
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage|null $image      Image
     * @param string                                              $filterName Imagine filter name
     * @param string|null                                         $fallback   Fallback
     *
     * @return string|null
     */
    public function buildImagine(AbstractImage $image = null, $filterName, $fallback = null)
    {
        if (empty($image) && !empty($fallback)) {
            return $fallback;
        }
        try {
            return $this->urlBuilder->buildUrlToFilter($image, DirectImagineFilter::NAME, [
                DirectImagineFilter::FILTER_NAME_PARAM => $filterName,
            ]);
        } catch (NotLoadableException $ex) {
            $this->logError($image, $ex);

            return null;
        }
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage|null $image    Image
     * @param bool                                                $absolute Whether to build absolute URL
     *
     * @return string|null
     */
    public function buildUrlToOriginal(AbstractImage $image = null, $absolute = false)
    {
        try {
            return $this->urlBuilder->buildUrlToOriginal($image, $absolute);
        } catch (NotLoadableException $ex) {
            $this->logError($image, $ex);

            return null;
        }
    }

    /**
     * @param \Darvin\ImageBundle\Entity\Image\AbstractImage|null $image Image
     * @param \Exception                                          $ex    Exception
     */
    private function logError(AbstractImage $image = null, \Exception $ex)
    {
        if (empty($image)) {
            $this->logger->error(sprintf('Unable to build URL for placeholder image: "%s".', $ex->getMessage()));

            return;
        }

        $this->logger->error(sprintf('Unable to build URL for image with ID "%d": "%s".', $image->getId(), $ex->getMessage()));
    }
}
