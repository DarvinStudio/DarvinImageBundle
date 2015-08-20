<?php
/**
 * @author    Lev Semin     <lev@darvin-studio.ru>
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Size\Manager;

use Darvin\ImageBundle\Configuration\ConfigurationPool;
use Darvin\ImageBundle\Size\Manager\Exception\SizeGroupNotFoundException;
use Darvin\ImageBundle\Size\Manager\Exception\SizeNotFoundException;
use Darvin\ImageBundle\Size\SizeGroup;

/**
 * Size manager
 */
class SizeManager implements SizeManagerInterface
{
    /**
     * @var \Darvin\ImageBundle\Configuration\ConfigurationPool
     */
    private $configurationPool;

    /**
     * @var \Darvin\ImageBundle\Size\SizeGroup[]
     */
    private $sizeGroups;

    /**
     * @var bool
     */
    private $initialized;

    /**
     * @param \Darvin\ImageBundle\Configuration\ConfigurationPool $configurationPool Configuration pool
     */
    public function __construct(ConfigurationPool $configurationPool)
    {
        $this->configurationPool = $configurationPool;
        $this->sizeGroups = array();
        $this->initialized = false;
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
            foreach ($this->sizeGroups as $group) {
                if (!$group->isGlobal()) {
                    continue;
                }

                $size = $group->findSizeByName($sizeName);

                if (!empty($size)) {
                    break;
                }
            }
            if (empty($size)) {
                $message = sprintf(
                    'Size "%s" not found in group "%s" and global groups. Sizes in group: "%s".',
                    $sizeName,
                    $groupName,
                    implode('", "', $group->getSizeNames())
                );

                throw new SizeNotFoundException($message);
            }
        }

        return $size;
    }

    /**
     * @param string $name Size group name
     *
     * @return \Darvin\ImageBundle\Size\SizeGroup
     * @throws \Darvin\ImageBundle\Size\Manager\Exception\SizeGroupNotFoundException
     */
    private function getGroup($name)
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
            $this->sizeGroups[$configuration->getImageSizeGroupName()] = new SizeGroup(
                $configuration->isImageSizesGlobal(),
                $configuration->getImageSizes()
            );
        }

        $this->initialized = true;
    }
}
