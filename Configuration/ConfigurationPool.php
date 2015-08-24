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

use Darvin\ConfigBundle\Security\Authorization\ConfigurationAuthorizationChecker;

/**
 * Configuration pool
 */
class ConfigurationPool
{
    /**
     * @var \Darvin\ConfigBundle\Security\Authorization\ConfigurationAuthorizationChecker
     */
    private $configurationAuthorizationChecker;

    /**
     * @var \Darvin\ImageBundle\Configuration\ImageConfigurationInterface[]
     */
    private $configurations;

    /**
     * @var bool
     */
    private $initialized;

    /**
     * @param \Darvin\ConfigBundle\Security\Authorization\ConfigurationAuthorizationChecker $configurationAuthorizationChecker Configuration authorization checker
     */
    public function __construct(ConfigurationAuthorizationChecker $configurationAuthorizationChecker)
    {
        $this->configurationAuthorizationChecker = $configurationAuthorizationChecker;
        $this->configurations = array();
        $this->initialized = false;
    }

    /**
     * @param \Darvin\ImageBundle\Configuration\ImageConfigurationInterface $configuration Configuration
     *
     * @throws \Darvin\ImageBundle\Configuration\ConfigurationException
     */
    public function add(ImageConfigurationInterface $configuration)
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
    public function getAll()
    {
        $this->init();

        return $this->configurations;
    }

    /**
     * @param string $sizeGroupName Size group name
     *
     * @return \Darvin\ImageBundle\Configuration\ImageConfigurationInterface
     * @throws \Darvin\ImageBundle\Configuration\ConfigurationException
     */
    public function get($sizeGroupName)
    {
        $this->init();

        if (!isset($this->configurations[$sizeGroupName])) {
            throw new ConfigurationException(
                sprintf('Configuration for size group name "%s" does not exist.', $sizeGroupName)
            );
        }

        return $this->configurations[$sizeGroupName];
    }

    private function init()
    {
        if ($this->initialized) {
            return;
        }
        foreach ($this->configurations as $sizeGroupName => $configuration) {
            if (!$this->configurationAuthorizationChecker->isAccessible($configuration)) {
                unset($this->configurations[$sizeGroupName]);
            }
        }

        $this->initialized = true;
    }
}
