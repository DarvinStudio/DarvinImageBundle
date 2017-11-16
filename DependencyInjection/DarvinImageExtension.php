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

use Darvin\Utils\DependencyInjection\ConfigInjector;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DarvinImageExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        (new ConfigInjector())->inject($this->processConfiguration(new Configuration(), $configs), $container, $this->getAlias());

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach ([
            'archive',
            'command',
            'image',
            'imagine',
            'namer',
            'orm',
            'url_builder',
            'validation',
        ] as $resource) {
            $loader->load($resource.'.yml');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $fileLocator = new FileLocator(__DIR__.'/../Resources/config/app');

        foreach ([
            'vich_uploader',
        ] as $extension) {
            if ($container->hasExtension($extension)) {
                $container->prependExtensionConfig($extension, Yaml::parse(file_get_contents($fileLocator->locate($extension.'.yml')))[$extension]);
            }
        }

        $config = $this->processConfiguration(
            new Configuration(),
            $container->getParameterBag()->resolveValue($container->getExtensionConfig($this->getAlias()))
        );

        $imagineConfig = [
            'cache'     => 'darvin_image_deprecated',
            'resolvers' => [
                'darvin_image_deprecated' => [
                    'web_path' => [
                        'web_root'     => $config['imagine']['cache_resolver']['web_root'],
                        'cache_prefix' => $config['imagine']['cache_resolver']['cache_prefix'],
                    ],
                ],
            ],
            'filter_sets' => [
                'thumbs' => [
                    'quality' => 87,
                ],
            ],
        ];

        $container->prependExtensionConfig('liip_imagine', $imagineConfig);
    }
}
