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

use Darvin\ImageBundle\UrlBuilder\Filter\FilterInterface;
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
    public const TAG_URL_BUILDER_FILTER = 'darvin_image.url_builder_filter';

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(FilterInterface::class)->addTag(self::TAG_URL_BUILDER_FILTER);

        (new ConfigInjector($container))->inject($this->processConfiguration(new Configuration(), $configs), $this->getAlias());

        (new ConfigLoader($container, __DIR__.'/../Resources/config/services'))->load([
            'archive/twig_extension',
            'image',
            'imageable',
            'imagine',
            'namer',
            'orm',
            'size',
            'url_builder',
            'validation',

            'archive/archiver/zip' => ['extension' => 'zip'],
            'archive/common'       => ['extension' => 'zip'],
        ]);

        if (extension_loaded('zip')) {
            $container->setAlias('darvin_image.archiver', 'darvin_image.archiver.zip');
        }
    }

    /**
     * {@inheritDoc}
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
            'cache'     => 'darvin_image_common',
            'resolvers' => [
                'darvin_image_common' => [
                    'web_path' => [
                        'web_root'     => $config['imagine']['cache_resolver']['web_root'],
                        'cache_prefix' => $config['imagine']['cache_resolver']['cache_prefix'],
                    ],
                ],
            ],
        ]);
    }
}
