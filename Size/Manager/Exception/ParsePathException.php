<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 05.04.15
 * Time: 9:46
 */

namespace Darvin\ImageBundle\Size\Manager\Exception;

/**
 * Parse path exception
 */
class ParsePathException extends SizeManagerException
{
    /**
     * @param string $path Path
     */
    public function __construct($path)
    {
        parent::__construct(sprintf('Unable to parse path "%s".', $path));
    }
}
