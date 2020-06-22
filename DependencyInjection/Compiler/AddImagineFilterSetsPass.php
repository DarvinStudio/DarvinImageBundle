<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Add Imagine filter sets compiler pass
 */
class AddImagineFilterSetsPass implements CompilerPassInterface
{
    private const FILTER_SETS_PARAM = 'darvin_image.imagine.filter_sets';

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $filterSetsConfig = $container->getParameter(self::FILTER_SETS_PARAM);

        foreach ($container->getParameter('darvin_image.output_formats') as $formatName => $formatAttr) {
            if (!$formatAttr['enabled']) {
                continue;
            }
            foreach ($filterSetsConfig as $setName => $setAttr) {
                $filterSetsConfig[implode('__', [$setName, $formatName])] = array_merge($setAttr, [
                    'format' => $formatName,
                ]);
            }
        }

        $container->setParameter(self::FILTER_SETS_PARAM, $filterSetsConfig);

        $filterSets = [];
        $defaults   = $container->getParameter('darvin_image.imagine.filter_defaults');

        foreach ($filterSetsConfig as $setName => $setAttr) {
            $filterSets[$setName] = array_merge_recursive($defaults, [
                'cache' => 'darvin_image_custom',
            ], $setAttr);
        }

        $container->setParameter(
            'liip_imagine.filter_sets',
            array_merge($filterSets, $container->getParameter('liip_imagine.filter_sets'))
        );
    }
}
