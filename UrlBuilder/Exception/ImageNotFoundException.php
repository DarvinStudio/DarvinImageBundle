<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.04.15
 * Time: 12:41
 */

namespace Darvin\ImageBundle\UrlBuilder\Exception;

/**
 * URL builder image not found exception
 */
class ImageNotFoundException extends UrlBuilderException
{
    /**
     * @param string $imagePathname Image pathname
     */
    public function __construct($imagePathname)
    {
        parent::__construct(sprintf('Image "%s" not found.', $imagePathname));
    }
}
