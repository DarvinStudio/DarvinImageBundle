<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ImageBundle\Validation\Constraints;

use Darvin\Utils\Strings\StringsUtil;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\ImageValidator;

/**
 * Darvin image constraint validator
 */
class DarvinImageValidator extends ImageValidator
{
    /**
     * @var array
     */
    private $options;

    /**
     * @param array $options         Options
     * @param int   $uploadMaxSizeMb Max upload size in MB
     */
    public function __construct(array $options, int $uploadMaxSizeMb)
    {
        $this->options = [];

        foreach ($options as $key => $value) {
            $this->options[lcfirst(StringsUtil::toCamelCase($key))] = $value;
        }
        if (!isset($this->options['maxSize'])) {
            $this->options['maxSize'] = sprintf('%dMi', $uploadMaxSizeMb);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        parent::validate($value, new Image($this->options));
    }
}
