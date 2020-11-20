<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Twig\Extension;

use Darvin\FileBundle\Form\Factory\ArchiveFormFactoryInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Archive Twig extension
 */
class ArchiveExtension extends AbstractExtension
{
    /**
     * @var \Darvin\FileBundle\Form\Factory\ArchiveFormFactoryInterface|null
     */
    private $archiveFormFactory;

    /**
     * @param \Darvin\FileBundle\Form\Factory\ArchiveFormFactoryInterface|null $archiveFormFactory Archive form factory
     */
    public function __construct(?ArchiveFormFactoryInterface $archiveFormFactory = null)
    {
        $this->archiveFormFactory = $archiveFormFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('image_archive_build_form', [$this, 'renderBuildForm'], [
                'needs_environment' => true,
                'is_safe'           => ['html'],
            ]),
        ];
    }

    /**
     * @param \Twig\Environment $twig     Twig
     * @param string            $template Template
     *
     * @return string
     */
    public function renderBuildForm(Environment $twig, string $template = '@DarvinFile/archive/build.html.twig'): string
    {
        if (null === $this->archiveFormFactory) {
            return '';
        }

        return $twig->render($template, [
            'form' => $this->archiveFormFactory->createBuildFormView(),
        ]);
    }
}
