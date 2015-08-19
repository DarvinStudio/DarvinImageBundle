<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
