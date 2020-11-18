<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Form\Type;

use Darvin\FileBundle\Form\Type\FileType;
use Darvin\ImageBundle\Size\ImageSizeDescriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Image form type
 */
class ImageType extends AbstractType
{
    /**
     * @var \Darvin\ImageBundle\Size\ImageSizeDescriberInterface
     */
    private $sizeDescriber;

    /**
     * @var int
     */
    private $uploadMaxSizeMb;

    /**
     * @param \Darvin\ImageBundle\Size\ImageSizeDescriberInterface $sizeDescriber   Image size describer
     * @param int                                                  $uploadMaxSizeMb Max upload size in MB
     */
    public function __construct(ImageSizeDescriberInterface $sizeDescriber, int $uploadMaxSizeMb)
    {
        $this->sizeDescriber = $sizeDescriber;
        $this->uploadMaxSizeMb = $uploadMaxSizeMb;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $sizeDescriber = $this->sizeDescriber;

        $resolver
            ->setDefaults([
                'accept'             => 'image/*',
                'filters'            => [],
                'width'              => 0,
                'height'             => 0,
                'upload_max_size_mb' => $this->uploadMaxSizeMb,
                'help'               => function (Options $options) use ($sizeDescriber) {
                    return $sizeDescriber->describeSize($options['filters'], $options['width'], $options['height'], $options['data_class']);
                },
            ])
            ->setAllowedTypes('filters', ['array', 'null', 'string'])
            ->setAllowedTypes('width', 'integer')
            ->setAllowedTypes('height', 'integer');
    }

    /**
     * {@inheritDoc}
     */
    public function getParent(): string
    {
        return FileType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix(): string
    {
        return 'darvin_image_image';
    }
}
