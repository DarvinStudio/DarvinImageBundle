<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle;

use Darvin\ImageBundle\DependencyInjection\Compiler\AddImagineFilterSetsPass;
use Darvin\ImageBundle\DependencyInjection\Compiler\AddUrlBuilderFiltersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Image bundle
 */
class DarvinImageBundle extends Bundle
{
    public const MAJOR_VERSION = 8;

    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container
            ->addCompilerPass(new AddImagineFilterSetsPass())
            ->addCompilerPass(new AddUrlBuilderFiltersPass());
    }
}
