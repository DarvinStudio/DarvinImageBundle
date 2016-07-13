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
 * Add image configurations compiler pass
 */
class AddImageConfigurationsPass implements CompilerPassInterface
{
    const POOL_ID = 'darvin_image.configuration.pool';

    const TAG_IMAGE_CONFIGURATION = 'darvin_image.configuration';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::POOL_ID)) {
            return;
        }

        $imageConfigurationIds = $container->findTaggedServiceIds(self::TAG_IMAGE_CONFIGURATION);

        if (empty($imageConfigurationIds)) {
            return;
        }

        $poolDefinition = $container->getDefinition(self::POOL_ID);

        foreach ($imageConfigurationIds as $id => $attr) {
            $poolDefinition->addMethodCall('addConfiguration', [
                new Reference($id),
            ]
            );
        }
    }
}
