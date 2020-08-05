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

use Darvin\ImageBundle\Size\ImageSizeDescriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('file', FileType::class, [
            'label'              => false,
            'upload_max_size_mb' => $this->uploadMaxSizeMb,
            'required'           => false,
            'attr'               => [
                'accept' => 'image/*',
            ],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars = array_merge($view->vars, [
            'disableable' => $options['disableable'],
            'editable'    => $options['editable'],
        ]);

        $help = (string)$view->children['file']->vars['help'];

        if ('' === $help) {
            return;
        }

        $view->children['file']->vars['help'] = null;

        $view->vars['help'] = (string)$view->vars['help'];

        if ('' !== $view->vars['help']) {
            $view->vars['help'] .= '<br>';
        }

        $view->vars['help'] .= $help;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $sizeDescriber = $this->sizeDescriber;

        $resolver
            ->setDefaults([
                'csrf_protection' => false,
                'required'        => false,
                'disableable'     => true,
                'editable'        => true,
                'filters'         => [],
                'width'           => 0,
                'height'          => 0,
                'help'            => function (Options $options) use ($sizeDescriber) {
                    return $sizeDescriber->describeSize($options['filters'], $options['width'], $options['height'], $options['data_class']);
                },
            ])
            ->setAllowedTypes('disableable', 'boolean')
            ->setAllowedTypes('editable', 'boolean')
            ->setAllowedTypes('filters', ['array', 'null', 'string'])
            ->setAllowedTypes('width', 'integer')
            ->setAllowedTypes('height', 'integer')
            ->remove('data_class')
            ->setRequired('data_class');
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix(): string
    {
        return 'darvin_image_image';
    }
}
