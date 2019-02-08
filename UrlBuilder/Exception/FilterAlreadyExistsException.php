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
 * URL builder filter already exists exception
 */
class FilterAlreadyExistsException extends \Exception
{
    /**
     * @param string $filterName Filter name
     */
    public function __construct(string $filterName)
    {
        parent::__construct(sprintf('Filter "%s" already exists.', $filterName));
    }
}
