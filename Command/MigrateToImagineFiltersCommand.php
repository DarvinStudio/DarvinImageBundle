<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Command;

use Darvin\ImageBundle\Size\Size;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * Migrate to Imagine filters command
 */
class MigrateToImagineFiltersCommand extends ContainerAwareCommand
{
    /**
     * @var array
     */
    private static $imageSizeNameReplacements = [
        'catalog_image_catalog_show'          => 'catalog_show',
        'catalog_image_catalog_show_children' => 'catalog_child',
    ];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('darvin:image:migrate:to-imagine')
            ->setDescription('Helps you to migrate your templates and configuration files from resize filter to Imagine filters.')
            ->setDefinition([
                new InputArgument('template_dir', InputArgument::OPTIONAL, 'Template directory'),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $templateDir = $input->getArgument('template_dir');

        if (empty($templateDir)) {
            $templateDir = $this->getContainer()->getParameter('kernel.project_dir').'/app/Resources';
        }

        $filterSets = [];
        $imageSizes = $this->getImageSizes();

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ((new Finder())->in($templateDir)->files()->name('*.html.twig') as $file) {
            $template = file_get_contents($file->getPathname());
            preg_match_all('/\|\s*image_(crop|resize)\(\'(.+?)\'\)/', $template, $matches);
            list($templateFilters, $modes, $imageSizeNames) = $matches;

            if (empty($templateFilters)) {
                continue;
            }

            $templateReplacements = [];

            foreach ($imageSizeNames as $key => $imageSizeName) {
                $newImageSizeName = isset(self::$imageSizeNameReplacements[$imageSizeName])
                    ? self::$imageSizeNameReplacements[$imageSizeName]
                    : $imageSizeName;

                $filterSets[$newImageSizeName] = [
                    'filters' => [
                        'thumbnail' => [
                            'size' => $imageSizes[$imageSizeName],
                            'mode' => 'crop' === $modes[$key] ? 'outbound' : 'inset',
                        ],
                    ],
                ];

                $templateFilter = $templateFilters[$key];
                $templateReplacements[$templateFilter] = sprintf('|image_filter(\'%s\')', $newImageSizeName);
            }

            file_put_contents($file->getPathname(), strtr($template, $templateReplacements));

            $io->comment(sprintf('Updated template "%s".', $file->getRelativePathname()));
        }
        if (empty($filterSets)) {
            $io->error('No image filters found.');

            return;
        }

        ksort($filterSets);

        $io->writeln(Yaml::dump([
            'darvin_image' => [
                'imagine' => [
                    'filter_sets' => $filterSets,
                ],
            ],
        ], 6));
    }

    /**
     * @return array
     */
    private function getImageSizes()
    {
        $sizes = [];

        foreach ($this->getConfigurationPool()->getAllConfigurations() as $config) {
            foreach ($config->getValues() as $value) {
                if (!is_array($value)) {
                    continue;
                }
                foreach ($value as $item) {
                    if ($item instanceof Size) {
                        $sizes[$item->getName()] = [(int)$item->getWidth(), (int)$item->getHeight()];
                    }
                }
            }
        }

        return $sizes;
    }

    /**
     * @return \Darvin\ConfigBundle\Configuration\ConfigurationPool
     */
    private function getConfigurationPool()
    {
        return $this->getContainer()->get('darvin_config.configuration.pool');
    }
}