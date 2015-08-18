<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 05.04.15
 * Time: 8:43
 */

namespace Darvin\ImageBundle\Size\Manager\Exception;

/**
 * Size group not found exception
 */
class SizeGroupNotFoundException extends SizeManagerException
{
    /**
     * @param string $groupName Size group name
     */
    public function __construct($groupName)
    {
        parent::__construct(sprintf('Size group "%s" not found.'));
    }
}
