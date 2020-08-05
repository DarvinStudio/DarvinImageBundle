<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Form\Factory;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\RouterInterface;

/**
 * Archive form factory
 */
class ArchiveFormFactory implements ArchiveFormFactoryInterface
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
     * {@inheritDoc}
     */
    public function createBuildFormView(): FormView
    {
        return $this->createBuildForm()->createView();
    }

    /**
     * {@inheritDoc}
     */
    public function createBuildForm(): FormInterface
    {
        return $this->genericFormFactory->createNamed('darvin_image_archive_build', FormType::class, null, [
            'action'          => $this->router->generate('darvin_image_archive_build'),
            'csrf_protection' => false,
        ]);
    }
}
