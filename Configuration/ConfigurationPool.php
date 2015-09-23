<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Configuration;

/**
 * Configuration pool
 */
class ConfigurationPool
{
    /**
     * @var \Darvin\ImageBundle\Configuration\ImageConfigurationInterface[]
     */
    private $configurations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->configurations = array();
    }

    /**
     * @param \Darvin\ImageBundle\Configuration\ImageConfigurationInterface $configuration Configuration
     *
     * @throws \Darvin\ImageBundle\Configuration\ConfigurationException
     */
    public function addConfiguration(ImageConfigurationInterface $configuration)
    {
        if (isset($this->configurations[$configuration->getImageSizeGroupName()])) {
            throw new ConfigurationException(
                sprintf('Configuration for size group name "%s" already added.', $configuration->getImageSizeGroupName())
            );
        }

        $this->configurations[$configuration->getImageSizeGroupName()] = $configuration;
    }

    /**
     * @return \Darvin\ImageBundle\Configuration\ImageConfigurationInterface[]
     */
    public function getAllConfiguration()
    {
        return $this->configurations;
    }

    /**
     * @param string $sizeGroupName Size group name
     *
     * @return \Darvin\ImageBundle\Configuration\ImageConfigurationInterface
     * @throws \Darvin\ImageBundle\Configuration\ConfigurationException
     */
    public function getConfiguration($sizeGroupName)
    {
        if (!isset($this->configurations[$sizeGroupName])) {
            throw new ConfigurationException(
                sprintf('Configuration for size group name "%s" does not exist.', $sizeGroupName)
            );
        }

        return $this->configurations[$sizeGroupName];
    }
}
