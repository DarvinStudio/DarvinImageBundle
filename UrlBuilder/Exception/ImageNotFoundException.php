<?php
/**
 * @author    Lev Semin     <lev@darvin-studio.ru>
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
