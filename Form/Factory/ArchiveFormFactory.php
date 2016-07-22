<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Form\Factory;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Archive form factory
 */
class ArchiveFormFactory
{
    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $genericFormFactory;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @param \Symfony\Component\Form\FormFactoryInterface $genericFormFactory Generic form factory
     * @param \Symfony\Component\Routing\RouterInterface   $router             Router
     */
    public function __construct(FormFactoryInterface $genericFormFactory, RouterInterface $router)
    {
        $this->genericFormFactory = $genericFormFactory;
        $this->router = $router;
    }

    /**
     * @return \Symfony\Component\Form\FormView
     */
    public function createBuildFormView()
    {
        return $this->createBuildForm()->createView();
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createBuildForm()
    {
        return $this->genericFormFactory->createNamed(
            'darvin_image_archive_build',
            'Symfony\Component\Form\Extension\Core\Type\FormType',
            null,
            [
                'action'        => $this->router->generate('darvin_image_archive_build'),
                'csrf_token_id' => md5(__FILE__.__METHOD__),
            ]
        );
    }
}
