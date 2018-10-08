<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\DependencyInjection;

use Darvin\ImageBundle\Entity\Image\AbstractImage;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('darvin_image');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
            ->children()
                ->arrayNode('imagine')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('cache_resolver')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('web_root')->defaultValue('%kernel.project_dir%/public')->cannotBeEmpty()->end()
                                ->scalarNode('cache_prefix')->defaultValue('cache')->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                        ->arrayNode('filter_defaults')->addDefaultsIfNotSet()
                            ->children()
                                ->integerNode('jpeg_quality')->defaultValue(87)->min(0)->max(100)->end()
                                ->integerNode('png_compression_level')->defaultValue(9)->min(0)->max(9)->end()
                                ->arrayNode('filters')->useAttributeAsKey('name')->prototype('variable')->end()->end()
                            ->end()
                        ->end()
                        ->arrayNode('filter_sets')->useAttributeAsKey('name')
                            ->prototype('array')->useAttributeAsKey('name')->prototype('variable')->end()
                                ->beforeNormalization()
                                    ->always(function ($filterSet) {
                                        if (is_array($filterSet) && isset($filterSet['entities']) && !is_array($filterSet['entities'])) {
                                            $filterSet['entities'] = [$filterSet['entities']];
                                        }

                                        return $filterSet;
                                    })
                                ->end()
                                ->validate()
                                    ->ifTrue(function (array $filterSet) {
                                        if (!isset($filterSet['entities'])) {
                                            return false;
                                        }
                                        foreach ($filterSet['entities'] as $class) {
                                            if (!class_exists($class)) {
                                                throw new \RuntimeException(sprintf('Entity class "%s" does not exist.', $class));
                                            }
                                            if (AbstractImage::class !== $class && !in_array(AbstractImage::class, class_parents($class))) {
                                                throw new \RuntimeException(
                                                    sprintf('Entity class "%s" must be instance of "%s" or it\'s descendant.', $class, AbstractImage::class)
                                                );
                                            }
                                        }

                                        return false;
                                    })
                                    ->thenInvalid('')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('archive')->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('cache_dir')->defaultValue('%kernel.cache_dir%/images')->end()
                        ->scalarNode('filename_suffix')->defaultValue('images')->end()
                    ->end()
                ->end()
                ->arrayNode('constraints')->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('max_width')->defaultValue(10000)->end()
                        ->integerNode('max_height')->defaultValue(10000)->end()
                        ->arrayNode('mime_types')
                            ->prototype('scalar')->end()
                            ->requiresAtLeastOneElement()
                            ->defaultValue(['image/gif', 'image/jpeg', 'image/pjpeg', 'image/png'])
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('placeholder')
                    ->defaultNull()
                    ->info('Placeholder image pathname relative to the web directory.')
                    ->example('assets/images/placeholder.png')
                ->end()
                ->scalarNode('upload_path')->isRequired()->cannotBeEmpty()->end()
            ->end();

        return $treeBuilder;
    }
}
