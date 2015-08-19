<?php
/**
 * @author    Lev Semin <lev@darvin-studio.ru>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Size\Manager;

use Darvin\ConfigBundle\Configuration\ConfigurationPool;
use Darvin\ImageBundle\Configuration\ImageConfigurationInterface;
use Darvin\ImageBundle\Size\Manager\Exception\SizeGroupNotFoundException;
use Darvin\ImageBundle\Size\Manager\Exception\SizeNotFoundException;
use Darvin\ImageBundle\Size\SizeGroup;

/**
 * Size manager
 */
class SizeManager implements SizeManagerInterface
{
    /**
     * @var \Darvin\ConfigBundle\Configuration\ConfigurationPool
     */
    private $configurationPool;

    /**
     * @var \Darvin\ImageBundle\Configuration\ImageConfigurationInterface[]
     */
    private $imageConfigurations;

    /**
     * @var \Darvin\ImageBundle\Size\SizeGroup[]
     */
    private $sizeGroups;

    /**
     * @var bool
     */
    private $initialized;

    /**
     * @param \Darvin\ConfigBundle\Configuration\ConfigurationPool $configurationPool Configuration pool
     */
    public function __construct(ConfigurationPool $configurationPool)
    {
        $this->configurationPool = $configurationPool;
        $this->imageConfigurations = array();
        $this->sizeGroups = array();
        $this->initialized = false;
    }

    /**
     * {@inheritdoc}
     */
    public function saveSizes()
    {
        $this->init();

        array_map(function (ImageConfigurationInterface $configuration) {
            $configuration->save();
        }, $this->imageConfigurations);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllGroups()
    {
        $this->init();

        return $this->sizeGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize($groupName, $sizeName)
    {
        $this->init();

        $group = $this->getGroup($groupName);
        $size = $group->findSizeByName($sizeName);

        if (empty($size)) {
            $message = sprintf(
                'Size "%s" not found in group "%s". Sizes in group: "%s".',
                $sizeName,
                $groupName,
                implode('", "', $group->getSizeNames())
            );

            throw new SizeNotFoundException($message);
        }

        return $size;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup($name)
    {
        $this->init();

        if (!isset($this->sizeGroups[$name])) {
            throw new SizeGroupNotFoundException($name);
        }

        return $this->sizeGroups[$name];
    }

    private function init()
    {
        if ($this->initialized) {
            return;
        }
        foreach ($this->configurationPool->getAll() as $configuration) {
            if (!$configuration instanceof ImageConfigurationInterface) {
                continue;
            }

            $this->imageConfigurations[] = $configuration;
            $this->sizeGroups[$configuration->getName()] = new SizeGroup($configuration->getSizes());
        }

        $this->initialized = true;
    }
}
