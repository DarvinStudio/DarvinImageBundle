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
 * Add size resolvers compiler pass
 */
class AddSizeResolversPass implements CompilerPassInterface
{
    const POOL_ID = 'darvin_image.size.resolver.pool';

    const TAG_SIZE_RESOLVER = 'darvin_image.size_resolver';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::POOL_ID)) {
            return;
        }

        $sizeResolverIds = $container->findTaggedServiceIds(self::TAG_SIZE_RESOLVER);

        if (empty($sizeResolverIds)) {
            return;
        }

        $poolDefinition = $container->getDefinition(self::POOL_ID);

        foreach ($sizeResolverIds as $id => $attr) {
            $poolDefinition->addMethodCall('addResolver', [
                new Reference($id),
            ]
            );
        }
    }
}
