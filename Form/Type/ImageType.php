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
     * @param \Darvin\ImageBundle\Size\ImageSizeDescriberInterface $sizeDescriber Image size describer
     */
    public function __construct(ImageSizeDescriberInterface $sizeDescriber)
    {
        $this->sizeDescriber = $sizeDescriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['disableable'] = $options['disableable'];

        $help = $view->children['file']->vars['help'];

        if (empty($help)) {
            return;
        }

        $view->children['file']->vars['help'] = null;

        if (!empty($view->vars['help'])) {
            $view->vars['help'] .= '<br>';
        }

        $view->vars['help'] .= $help;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $sizeDescriber = $this->sizeDescriber;

        $resolver
            ->setDefaults([
                'csrf_token_id' => md5(__FILE__.$this->getBlockPrefix()),
                'required'      => false,
                'disableable'   => true,
                'filters'       => [],
                'width'         => 0,
                'height'        => 0,
                'help'          => function (Options $options) use ($sizeDescriber) {
                    return $sizeDescriber->describeSize($options['filters'], $options['width'], $options['height'], $options['data_class']);
                },
            ])
            ->setAllowedTypes('disableable', 'boolean')
            ->setAllowedTypes('filters', ['array', 'null', 'string'])
            ->setAllowedTypes('width', 'integer')
            ->setAllowedTypes('height', 'integer')
            ->remove('data_class')
            ->setRequired('data_class');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'darvin_image_image';
    }
}
