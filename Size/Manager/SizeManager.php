<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 05.04.15
 * Time: 8:48
 */

namespace Darvin\ImageBundle\Size\Manager;

use Darvin\ConfigBundle\Configuration\ConfigurationPool;
use Darvin\ImageBundle\Configuration\ImageConfigurationInterface;
use Darvin\ImageBundle\Size\Manager\Exception\BlockNotFoundException;
use Darvin\ImageBundle\Size\Manager\Exception\ParsePathException;
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
    public function getSizeByPath($path)
    {
        $this->init();

        $names = explode('.', $path);

        if (2 !== count($names)) {
            throw new ParsePathException($path);
        }

        return $this->getSize($names[0], $names[1]);
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
            throw new SizeNotFoundException($groupName, $sizeName);
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
            throw new BlockNotFoundException($name);
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
