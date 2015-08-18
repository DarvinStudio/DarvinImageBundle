<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 18.08.15
 * Time: 10:59
 */

namespace Darvin\ImageBundle\Form\Type;

use Darvin\ImageBundle\Size\Size;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Size form type
 */
class SizeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'hidden')
            ->add('width', null, array(
                'label' => 'size.model.width',
            ))
            ->add('height', null, array(
                'label' => 'size.model.height',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => Size::CLASS_NAME,
            'intention'          => md5(__FILE__),
            'translation_domain' => 'messages',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'darvin_image_size';
    }
}
