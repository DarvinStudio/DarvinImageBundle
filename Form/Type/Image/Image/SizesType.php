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

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Darvin\ImageBundle\Entity\Image\Size;
use Darvin\ImageBundle\Form\Type\Image\SizeType;
use Darvin\ImageBundle\Size\Manager\SizeManagerInterface;
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
     * @var \Darvin\ImageBundle\Size\Manager\SizeManagerInterface
     */
    private $sizeManager;

    /**
     * @param \Darvin\ImageBundle\Size\Manager\SizeManagerInterface $sizeManager Size manager
     */
    public function __construct(SizeManagerInterface $sizeManager)
    {
        $this->sizeManager = $sizeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $sizeManager = $this->sizeManager;

        $builder
            ->add('sizes', 'collection', array(
                'type' => new SizeType(),
            ))
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($sizeManager) {
                /** @var \Darvin\ImageBundle\Entity\Image\AbstractImage $image */
                $image = $event->getData();

                $configuration = $sizeManager->getConfiguration($image->getSizeGroupName());

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
