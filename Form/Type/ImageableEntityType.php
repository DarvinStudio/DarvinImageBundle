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

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\ImageBundle\Imageable\ImageableInterface;
use Darvin\ImageBundle\UrlBuilder\Filter\DirectImagineFilter;
use Darvin\ImageBundle\UrlBuilder\UrlBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Imageable entity form type
 */
class ImageableEntityType extends AbstractType
{
    public const OPTION_IMAGE_PROPERTY = 'image_property';
    public const OPTION_IMAGINE_FILTER = 'imagine_filter';

    /**
     * @var \Darvin\ImageBundle\UrlBuilder\UrlBuilderInterface
     */
    private $imageUrlBuilder;

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @param \Darvin\ImageBundle\UrlBuilder\UrlBuilderInterface          $imageUrlBuilder  Image URL builder
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor Property accessor
     */
    public function __construct(UrlBuilderInterface $imageUrlBuilder, PropertyAccessorInterface $propertyAccessor)
    {
        $this->imageUrlBuilder = $imageUrlBuilder;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        foreach ([
            self::OPTION_IMAGE_PROPERTY,
            self::OPTION_IMAGINE_FILTER,
        ] as $option) {
            $resolver
                ->setDefault($option, null)
                ->setAllowedTypes($option, ['string', 'null']);
        }

        $propertyAccessor = $this->propertyAccessor;
        $urlBuilder       = $this->imageUrlBuilder;

        $resolver
            ->setNormalizer('choice_attr', function (Options $options) use ($propertyAccessor, $urlBuilder) {
                return function ($entity) use ($options, $propertyAccessor, $urlBuilder) {
                    $attr = [];

                    if ($entity instanceof ImageableInterface) {
                        $image = $entity->getImage();
                    } else {
                        if (null === $options[self::OPTION_IMAGE_PROPERTY]) {
                            throw new \InvalidArgumentException(sprintf('Option "%s" is required.', self::OPTION_IMAGE_PROPERTY));
                        }

                        $image = $propertyAccessor->getValue($entity, $options[self::OPTION_IMAGE_PROPERTY]);
                    }
                    if (null !== $image) {
                        if (!$image instanceof AbstractImage) {
                            throw new \LogicException(
                                sprintf('Image must be instance of "%s", got "%s".', AbstractImage::class, gettype($image))
                            );
                        }

                        $url = null !== $options[self::OPTION_IMAGINE_FILTER]
                            ? $urlBuilder->buildFilteredUrl($image, DirectImagineFilter::NAME, [
                                DirectImagineFilter::FILTER_NAME_PARAM => $options[self::OPTION_IMAGINE_FILTER],
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
