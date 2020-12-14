<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Form\Type;

use Darvin\FileBundle\Form\Type\FileType;
use Darvin\ImageBundle\Size\ImageSizeDescriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
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
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        if (!isset($view->vars['help_translation_parameters']['%size_help%'])) {
            $view->vars['help_translation_parameters']['%size_help%'] = $this->sizeDescriber->describeSize(
                $options['filters'],
                $options['width'],
                $options['height'],
                $options['data_class']
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'accept'             => 'image/*',
                'filters'            => [],
                'help'               => 'form.image.help',
                'upload_max_size_mb' => $this->uploadMaxSizeMb,
                'width'              => 0,
                'height'             => 0,
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
