<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.04.15
 * Time: 12:46
 */

namespace Darvin\ImageBundle\UrlBuilder\Exception;

/**
 * Filter already exists exception
 */
class FilterAlreadyExistsException extends \Exception
{
    /**
     * @param string $filterName Filter name
     */
    public function __construct($filterName)
    {
        parent::__construct(sprintf('Filter "%s" already exists.', $filterName));
    }
}
