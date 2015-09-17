<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle;

use Darvin\ImageBundle\DependencyInjection\Compiler\AddImageConfigurationsPass;
use Darvin\ImageBundle\DependencyInjection\Compiler\AddSizeResolversPass;
use Darvin\ImageBundle\DependencyInjection\Compiler\AddUrlBuilderFiltersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Image bundle
 */
class DarvinImageBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container
            ->addCompilerPass(new AddImageConfigurationsPass())
            ->addCompilerPass(new AddSizeResolversPass())
            ->addCompilerPass(new AddUrlBuilderFiltersPass());
    }
}
