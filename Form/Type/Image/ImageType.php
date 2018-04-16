<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Form\Type\Image;

use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Image form type
 */
class ImageType extends AbstractType
{
    /**
     * @var \Liip\ImagineBundle\Imagine\Filter\FilterConfiguration
     */
    private $filterConfig;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @param \Liip\ImagineBundle\Imagine\Filter\FilterConfiguration $filterConfig Imagine filter configuration
     * @param \Symfony\Component\Translation\TranslatorInterface     $translator   Translator
     */
    public function __construct(FilterConfiguration $filterConfig, TranslatorInterface $translator)
    {
        $this->filterConfig = $filterConfig;
        $this->translator = $translator;
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
    public function configureOptions(OptionsResolver $resolver)
    {
        $getMaxSize = [$this, 'getMaxSize'];
        $translator = $this->translator;

        $resolver
            ->setDefaults([
                'csrf_token_id' => md5(__FILE__.$this->getBlockPrefix()),
                'required'      => false,
                'description'   => function (Options $options) use ($getMaxSize, $translator) {
                    $width = $height = 0;

                    if (isset($options['filters'])) {
                        list($width, $height) = $getMaxSize((array)$options['filters']);
                    }
                    if (isset($options['width'])) {
                        $width = $options['width'];
                    }
                    if (isset($options['height'])) {
                        $height = $options['height'];
                    }
                    if ($width > 0 && $height > 0) {
                        return $translator->trans('image.form.description.size', [
                            '%width%'  => $width,
                            '%height%' => $height,
                        ], 'darvin_image');
                    }

                    return null;
                },
            ])
            ->remove([
                'data_class',
            ])
            ->setRequired([
                'data_class',
            ])
            ->setDefined([
                'filters',
                'width',
                'height',
            ])
            ->setAllowedTypes('filters', [
                'array',
                'string',
            ])
            ->setAllowedTypes('width', 'integer')
            ->setAllowedTypes('height', 'integer');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'darvin_image_image';
    }

    /**
     * @param string[] $filterSetNames Imagine filter set names
     *
     * @return int[]
     */
    protected function getMaxSize(array $filterSetNames)
    {
        $maxWidth = $maxHeight = 0;

        foreach ($filterSetNames as $filterSetName) {
            list($width, $height) = $this->getSize($filterSetName);

            if ($width > $maxWidth) {
                $maxWidth = $width;
            }
            if ($height > $maxHeight) {
                $maxHeight = $height;
            }
        }

        return [$maxWidth, $maxHeight];
    }

    /**
     * @param string $filterSetName Imagine filter set name
     *
     * @return int[]
     */
    private function getSize($filterSetName)
    {
        $filterSet = $this->filterConfig->get($filterSetName);

        if (isset($filterSet['filters'])) {
            foreach ($filterSet['filters'] as $filterName => $params) {
                if ('thumbnail' === $filterName) {
                    return $params['size'];
                }
            }
        }

        return [0, 0];
    }
}
