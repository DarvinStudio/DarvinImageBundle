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
 * URL builder filter not found exception
 */
class FilterNotFoundException extends UrlBuilderException
{
    /**
     * @param string $filterName Filter name
     */
    public function __construct($filterName)
    {
        parent::__construct(sprintf('Filter "%s" not found.', $filterName));
    }
}
