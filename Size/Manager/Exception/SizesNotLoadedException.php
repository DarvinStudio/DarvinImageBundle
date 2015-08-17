<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 05.04.15
 * Time: 8:42
 */

namespace Darvin\ImageBundle\Size\Manager\Exception;

/**
 * Sizes not loaded exception
 */
class SizesNotLoadedException extends \LogicException
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('Sizes is not loaded yet.');
    }
}
