<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
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
    private const TAG_URL_BUILDER_FILTER = 'darvin_image.url_builder_filter';
    private const URL_BUILDER_ID         = 'darvin_image.url_builder.builder';

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $urlBuilder = $container->getDefinition(self::URL_BUILDER_ID);

        foreach (array_keys($container->findTaggedServiceIds(self::TAG_URL_BUILDER_FILTER)) as $id) {
            $urlBuilder->addMethodCall('addFilter', [new Reference($id)]);
        }
    }
}
