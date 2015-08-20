<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Form\Type\Image\Image;

use Darvin\ImageBundle\Configuration\ConfigurationPool;
use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\ImageBundle\Entity\Image\Size;
use Darvin\ImageBundle\Form\Type\Image\SizeType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Image sizes form type
 */
class SizesType extends AbstractType
{
    /**
     * @var \Darvin\ImageBundle\Configuration\ConfigurationPool
     */
    private $configurationPool;

    /**
     * @param \Darvin\ImageBundle\Configuration\ConfigurationPool $configurationPool Configuration pool
     */
    public function __construct(ConfigurationPool $configurationPool)
    {
        $this->configurationPool = $configurationPool;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $configurationPool = $this->configurationPool;

        $builder
            ->add('sizes', 'collection', array(
                'type' => new SizeType(),
            ))
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($configurationPool) {
                /** @var \Darvin\ImageBundle\Entity\Image\AbstractImage $image */
                $image = $event->getData();

                $configuration = $configurationPool->get($image->getSizeGroupName());

                $sizes = array();

                foreach ($image->getSizes() as $size) {
                    $sizes[$size->getName()] = $size;
                }
                foreach ($configuration->getSizes() as $model) {
                    if (isset($sizes[$model->getName()])) {
                        continue;
                    }

                    $size = Size::fromModel($model);
                    $sizes[$size->getName()] = $size;
                }

                $image->setSizes(new ArrayCollection($sizes));
            });
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
        return 'darvin_image_image_sizes';
    }
}
