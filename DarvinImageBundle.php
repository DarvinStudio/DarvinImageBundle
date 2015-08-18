<?php

namespace Darvin\ImageBundle;

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
            ->addCompilerPass(new SizeResolverPass())
            ->addCompilerPass(new UrlBuilderFilterPass());
    }
}
