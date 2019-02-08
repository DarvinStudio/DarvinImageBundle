<?php declare(strict_types=1);
/**
 * @author    Lev Semin     <lev@darvin-studio.ru>
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\UrlBuilder\Exception;

/**
 * URL builder image not found exception
 */
class ImageNotFoundException extends \Exception
{
    /**
     * @param string $imagePathname Image pathname
     */
    public function __construct(string $imagePathname)
    {
        $message = !empty($imagePathname)
            ? sprintf('Image "%s" not found.', $imagePathname)
            : 'Image pathname is empty and placeholder is not configured.';

        parent::__construct($message);
    }
}
