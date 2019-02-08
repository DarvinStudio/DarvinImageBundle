<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2018, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\DependencyInjection;

use Darvin\Utils\DependencyInjection\ConfigInjector;
use Darvin\Utils\DependencyInjection\ConfigLoader;
use Darvin\Utils\DependencyInjection\ExtensionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

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
    public function load(array $configs, ContainerBuilder $container): void
    {
        (new ConfigInjector($container))->inject($this->processConfiguration(new Configuration(), $configs), $this->getAlias());

        (new ConfigLoader($container, __DIR__.'/../Resources/config'))->load([
            'archive/twig_extension',
            'image',
            'imagine',
            'namer',
            'orm',
            'size',
            'url_builder',
            'validation',

            'dev/image'            => ['env' => 'dev'],

            'archive/archiver/zip' => ['extension' => 'zip'],
            'archive/form_factory' => ['extension' => 'zip'],
        ]);

        if (extension_loaded('zip')) {
            $container->setAlias('darvin_image.archiver', 'darvin_image.archiver.zip');
            $container->getAlias('darvin_image.archiver')->setPublic(true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        (new ExtensionConfigurator($container, __DIR__.'/../Resources/config/app'))->configure([
            'darvin_image',
            'vich_uploader',
        ]);

        $config = $this->processConfiguration(
            new Configuration(),
            $container->getParameterBag()->resolveValue($container->getExtensionConfig($this->getAlias()))
        );

        $container->prependExtensionConfig('liip_imagine', [
            'cache'     => 'darvin_image',
            'resolvers' => [
                'darvin_image' => [
                    'web_path' => [
                        'web_root'     => $config['imagine']['cache_resolver']['web_root'],
                        'cache_prefix' => $config['imagine']['cache_resolver']['cache_prefix'],
                    ],
                ],
            ],
        ]);
    }
}
