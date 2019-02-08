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
use Darvin\Utils\ObjectNamer\ObjectNamerInterface;
use Darvin\Utils\Strings\StringsUtil;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Image edit form type
 */
class ImageEditType extends AbstractType
{
    /**
     * @var \Darvin\Utils\ObjectNamer\ObjectNamerInterface
     */
    private $objectNamer;

    /**
     * @var array
     */
    private $fields;

    /**
     * @param \Darvin\Utils\ObjectNamer\ObjectNamerInterface $objectNamer Object namer
     * @param array                                          $fields      Fields
     */
    public function __construct(ObjectNamerInterface $objectNamer, array $fields)
    {
        $this->objectNamer = $objectNamer;
        $this->fields = $fields;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $class = null !== $builder->getData() ? ClassUtils::getClass($builder->getData()) : AbstractImage::class;

        foreach ($this->fields[$class] ?? $this->fields[AbstractImage::class] as $name => $attr) {
            $builder->add($name, $attr['type'], $attr['options']);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $genericPrefix = 'image.entity.';

        $currentPrefix = null !== $form->getData() ? sprintf('%s.entity.', $this->objectNamer->name($form->getData())) : $genericPrefix;

        foreach ($view->children as $name => $field) {
            if (null !== $field->vars['label']) {
                continue;
            }

            $prefix = array_key_exists($name, $this->fields[AbstractImage::class]) ? $genericPrefix : $currentPrefix;

            $field->vars['label'] = $prefix.StringsUtil::toUnderscore($name);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_token_id' => md5(__FILE__.__METHOD__.$this->getBlockPrefix()),
            'data_class'    => AbstractImage::class,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix(): string
    {
        return 'darvin_image_edit';
    }
}
