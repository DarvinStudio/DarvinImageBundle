<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Twig\Extension;

use Darvin\ImageBundle\Form\Factory\ArchiveFormFactory;

/**
 * Archive Twig extension
 */
class ArchiveExtension extends \Twig_Extension
{
    /**
     * @var \Darvin\ImageBundle\Form\Factory\ArchiveFormFactory
     */
    private $archiveFormFactory;

    /**
     * @param \Darvin\ImageBundle\Form\Factory\ArchiveFormFactory $archiveFormFactory Archive form factory
     */
    public function __construct(ArchiveFormFactory $archiveFormFactory)
    {
        $this->archiveFormFactory = $archiveFormFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('image_archive_build_form', [$this, 'renderBuildForm'], [
                'is_safe'           => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    /**
     * @param \Twig_Environment $environment Environment
     * @param string            $template    Template
     *
     * @return string
     */
    public function renderBuildForm(\Twig_Environment $environment, $template = 'DarvinImageBundle:Archive/widget:build_form.html.twig')
    {
        return $environment->render($template, [
            'form' => $this->archiveFormFactory->createBuildFormView(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'darvin_image_archive_extension';
    }
}
