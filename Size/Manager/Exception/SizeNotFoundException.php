<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 05.04.15
 * Time: 8:43
 */

namespace DarvinCore\ImageBundle\Size\Manager\Exception;

/**
 * Size not found exception
 */
class SizeNotFoundException extends \Exception
{
    /**
     * {@inheritdoc}
     */
    public function __construct($blockName, $sizeName)
    {
        parent::__construct(sprintf('Size with name "%s" was not found in block "%s".', $sizeName, $blockName));
    }
}
