<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
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
     * @param array $options Options
     */
    public function __construct(array $options)
    {
        $this->options = [];

        foreach ($options as $key => $value) {
            $this->options[lcfirst(StringsUtil::toCamelCase($key))] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        parent::validate($value, new Image($this->options));
    }
}
