<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.04.15
 * Time: 12:41
 */

namespace Darvin\ImageBundle\UrlBuilder\Exception;

/**
 * Image not found exception
 */
class ImageNotFoundException extends \Exception
{
    /**
     * @param string $imagePath Image path
     */
    public function __construct($imagePath)
    {
        parent::__construct(sprintf('"%s" does not exist.', $imagePath));
    }
}
