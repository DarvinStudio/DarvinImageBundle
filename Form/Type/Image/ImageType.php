<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2018, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Form\Type\Image;

use Darvin\ImageBundle\Size\SizeDescriber;
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
     * @var \Darvin\ImageBundle\Size\SizeDescriber
     */
    private $sizeDescriber;

    /**
     * @param \Darvin\ImageBundle\Size\SizeDescriber $sizeDescriber Image size describer
     */
    public function __construct(SizeDescriber $sizeDescriber)
    {
        $this->sizeDescriber = $sizeDescriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', FileType::class, [
            'label'    => false,
            'required' => false,
            'attr'     => [
                'accept' => 'image/*',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $description = $view->children['file']->vars['description'];

        if (empty($description)) {
            return;
        }

        $view->children['file']->vars['description'] = null;

        if (!empty($view->vars['description'])) {
            $view->vars['description'] .= '<br>';
        }

        $view->vars['description'] .= $description;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $sizeDescriber = $this->sizeDescriber;

        $resolver
            ->setDefaults([
                'csrf_token_id' => md5(__FILE__.$this->getBlockPrefix()),
                'required'      => false,
                'filters'       => [],
                'width'         => 0,
                'height'        => 0,
                'description'   => function (Options $options) use ($sizeDescriber) {
                    return $sizeDescriber->describeSize($options['filters'], $options['width'], $options['height'], $options['data_class']);
                },
            ])
            ->setAllowedTypes('filters', [
                'array',
                'null',
                'string',
            ])
            ->setAllowedTypes('width', 'integer')
            ->setAllowedTypes('height', 'integer')
            ->remove([
                'data_class',
            ])
            ->setRequired([
                'data_class',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'darvin_image_image';
    }
}
