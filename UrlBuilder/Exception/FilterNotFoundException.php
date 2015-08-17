<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.04.15
 * Time: 12:45
 */

namespace Darvin\ImageBundle\UrlBuilder\Exception;

/**
 * Filter not found exception
 */
class FilterNotFoundException extends \Exception
{
    /**
     * @param string $filterName Filter name
     */
    public function __construct($filterName)
    {
        parent::__construct(sprintf('Filter "%s" not found.', $filterName));
    }
}
