<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 05.04.15
 * Time: 8:39
 */

namespace Darvin\ImageBundle\Size\Manager;

/**
 * Size manager
 */
interface SizeManagerInterface
{
    /**
     * @throws \Darvin\ImageBundle\Size\Manager\Exception\LoadSizeException
     */
    public function loadSizes();

    /**
     * @throws \Darvin\ImageBundle\Size\Manager\Exception\SizesNotLoadedException
     * @throws \Darvin\ImageBundle\Size\Manager\Exception\OnSizeSaveException
     */
    public function saveSizes();

    /**
     * @return \Darvin\ImageBundle\Size\SizeBlock[]
     * @throws \Darvin\ImageBundle\Size\Manager\Exception\SizesNotLoadedException
     */
    public function getAllBlocks();

    /**
     * @param string $name Size block name
     *
     * @return \Darvin\ImageBundle\Size\SizeBlock
     * @throws \Darvin\ImageBundle\Size\Manager\Exception\SizesNotLoadedException
     * @throws \Darvin\ImageBundle\Size\Manager\Exception\BlockNotFoundException
     */
    public function getBlock($name);

    /**
     * @param string $blockName Size block name
     * @param string $sizeName  Size name
     *
     * @return \Darvin\ImageBundle\Size\Size
     * @throws \Darvin\ImageBundle\Size\Manager\Exception\SizesNotLoadedException
     * @throws \Darvin\ImageBundle\Size\Manager\Exception\BlockNotFoundException
     */
    public function getSize($blockName, $sizeName);

    /**
     * @param string $path Path
     *
     * @return \Darvin\ImageBundle\Size\Size
     * @throws \Darvin\ImageBundle\Size\Manager\Exception\SizesNotLoadedException
     * @throws \Darvin\ImageBundle\Size\Manager\Exception\BlockNotFoundException
     * @throws \Darvin\ImageBundle\Size\Manager\Exception\ParsePathException
     */
    public function getSizeByPath($path);
}
