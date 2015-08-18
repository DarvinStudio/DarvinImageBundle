<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 18.08.15
 * Time: 9:57
 */

namespace Darvin\ImageBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * URL builder filter compiler pass
 */
class UrlBuilderFilterPass implements CompilerPassInterface
{
    const TAG_URL_BUILDER_FILTER = 'darvin_image.url_builder_filter';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $urlBuilder = $container->getDefinition('darvin_image.url_builder.builder');

        foreach ($container->findTaggedServiceIds(self::TAG_URL_BUILDER_FILTER) as $id => $attr) {
            $urlBuilder->addMethodCall('addFilter', array(
                new Reference($id),
            ));
        }
    }
}
