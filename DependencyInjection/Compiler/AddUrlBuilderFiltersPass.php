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
 * Add URL builder filters compiler pass
 */
class AddUrlBuilderFiltersPass implements CompilerPassInterface
{
    const TAG_URL_BUILDER_FILTER = 'darvin_image.url_builder_filter';

    const URL_BUILDER_ID = 'darvin_image.url_builder.builder';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::URL_BUILDER_ID)) {
            return;
        }

        $urlBuilderFilterIds = $container->findTaggedServiceIds(self::TAG_URL_BUILDER_FILTER);

        if (empty($urlBuilderFilterIds)) {
            return;
        }

        $urlBuilderDefinition = $container->getDefinition(self::URL_BUILDER_ID);

        foreach ($urlBuilderFilterIds as $id => $attr) {
            $urlBuilderDefinition->addMethodCall('addFilter', [
                new Reference($id),
            ]
            );
        }
    }
}
