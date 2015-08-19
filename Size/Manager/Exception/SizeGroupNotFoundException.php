<?php
/**
 * @author    Lev Semin <lev@darvin-studio.ru>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
