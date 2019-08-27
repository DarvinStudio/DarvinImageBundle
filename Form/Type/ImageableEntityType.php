<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Form\Type;

use Darvin\ImageBundle\Imageable\ImageableInterface;
use Darvin\ImageBundle\UrlBuilder\Filter\DirectImagineFilter;
use Darvin\ImageBundle\UrlBuilder\UrlBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Imageable entity form type
 */
class ImageableEntityType extends AbstractType
{
    /**
     * @var \Darvin\ImageBundle\UrlBuilder\UrlBuilderInterface
     */
    private $imageUrlBuilder;

    /**
     * @param \Darvin\ImageBundle\UrlBuilder\UrlBuilderInterface $imageUrlBuilder Image URL builder
     */
    public function __construct(UrlBuilderInterface $imageUrlBuilder)
    {
        $this->imageUrlBuilder = $imageUrlBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $urlBuilder = $this->imageUrlBuilder;

        $resolver
            ->setDefault('imagine_filter', null)
            ->setAllowedTypes('imagine_filter', ['string', 'null'])
            ->setNormalizer('choice_attr', function (Options $options) use ($urlBuilder) {
                $imagineFilter = $options['imagine_filter'];

                return function (ImageableInterface $entity) use ($imagineFilter, $urlBuilder) {
                    $attr  = [];
                    $image = $entity->getImage();

                    if (null !== $image) {
                        $url = null !== $imagineFilter
                            ? $urlBuilder->buildFilteredUrl($image, DirectImagineFilter::NAME, [
                                DirectImagineFilter::FILTER_NAME_PARAM => $imagineFilter,
                            ])
                            : $urlBuilder->buildOriginalUrl($image, false);

                        if (null !== $url) {
                            $attr['data-img-src'] = $url;
                        }
                    }

                    return $attr;
                };
            });
    }

    /**
     * {@inheritDoc}
     */
    public function getParent(): string
    {
        return EntityType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix(): string
    {
        return 'darvin_image_imageable_entity';
    }
}
