<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 17.08.15
 * Time: 12:04
 */

namespace Darvin\ImageBundle\Form\Type;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Image form type
 */
class ImageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'vich_image', array(
            'label'    => false,
            'required' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => AbstractImage::CLASS_NAME,
            'intention'  => md5(__FILE__),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'darvin_image_image';
    }
}
