<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 05.04.15
 * Time: 8:43
 */

namespace Darvin\ImageBundle\Size\Manager\Exception;

/**
 * Block not found exception
 */
class BlockNotFoundException extends \Exception
{
    /**
     * {@inheritdoc}
     */
    public function __construct($blockName)
    {
        parent::__construct(sprintf('Block with name "%s" was not found in collection.', $blockName));
    }
}
