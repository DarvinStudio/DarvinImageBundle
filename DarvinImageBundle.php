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

use Darvin\ImageBundle\DependencyInjection\Compiler\ConfigurationPoolPass;
use Darvin\ImageBundle\DependencyInjection\Compiler\SizeResolverPass;
use Darvin\ImageBundle\DependencyInjection\Compiler\UrlBuilderFilterPass;
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
            ->addCompilerPass(new ConfigurationPoolPass())
            ->addCompilerPass(new SizeResolverPass())
            ->addCompilerPass(new UrlBuilderFilterPass());
    }
}
