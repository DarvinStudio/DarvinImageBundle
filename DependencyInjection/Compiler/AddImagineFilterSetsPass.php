<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
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
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $filterSets = [];

        $config = $container->getParameter('darvin_image.imagine');

        foreach ($config['filter_sets'] as $name => $filterSet) {
            $filterSets[$name] = array_merge_recursive($config['filter_defaults'], [
                'cache' => 'darvin_image',
            ], $filterSet);
        }

        $container->setParameter(
            'liip_imagine.filter_sets',
            array_merge($filterSets, $container->getParameter('liip_imagine.filter_sets'))
        );
    }
}
