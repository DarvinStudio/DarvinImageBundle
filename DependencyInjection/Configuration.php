<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
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
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('darvin_image');

        /** @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $root */
        $root = $builder->getRootNode();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $root
            ->children()
                ->scalarNode('tmp_dir')->defaultValue(sprintf('%s/darvin/image', sys_get_temp_dir()))->cannotBeEmpty()->end()
                ->scalarNode('upload_path')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('placeholder')->defaultNull()
                    ->info('Placeholder image pathname relative to the web directory.')
                    ->example('assets/images/placeholder.png')
                ->end()
                ->arrayNode('archive')->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('filename_suffix')->defaultValue('images')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('constraints')->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('max_width')->defaultValue(10000)->min(1)->end()
                        ->integerNode('max_height')->defaultValue(10000)->min(1)->end()
                        ->arrayNode('mime_types')->requiresAtLeastOneElement()
                            ->prototype('scalar')->cannotBeEmpty()->end()
                            ->defaultValue(['image/gif', 'image/jpeg', 'image/pjpeg', 'image/png'])
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('action')
                    ->children()
                        ->arrayNode('edit')
                            ->children()
                                ->arrayNode('template')->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('full')->defaultValue('@DarvinImage/image/edit.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('partial')->defaultValue('@DarvinImage/image/_edit.html.twig')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->arrayNode('fields')->useAttributeAsKey('entity')
                                    ->prototype('array')->useAttributeAsKey('name')
                                        ->prototype('array')
                                            ->children()
                                                ->scalarNode('type')->defaultNull()->end()
                                                ->arrayNode('options')->useAttributeAsKey('name')->prototype('variable')->end()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                    ->validate()
                                        ->ifTrue(function (array $config) {
                                            foreach (array_keys($config) as $class) {
                                                if (!class_exists($class)) {
                                                    throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
                                                }
                                                if (AbstractImage::class !== $class && !is_subclass_of($class, AbstractImage::class)) {
                                                    throw new \InvalidArgumentException(sprintf('Class "%s" is not image entity class.', $class));
                                                }
                                            }
                                        })
                                        ->thenInvalid('')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
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
                                                throw new \InvalidArgumentException(sprintf('Entity class "%s" does not exist.', $class));
                                            }
                                            if (AbstractImage::class !== $class && !in_array(AbstractImage::class, class_parents($class))) {
                                                throw new \InvalidArgumentException(
                                                    sprintf('Entity class "%s" must be instance of "%s" or it\'s descendant.', $class, AbstractImage::class)
                                                );
                                            }
                                        }

                                        return false;
                                    })
                                    ->thenInvalid('');

        return $builder;
    }
}
