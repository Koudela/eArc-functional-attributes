<?php declare(strict_types=1);
/**
 * e-Arc Framework - the explicit Architecture Framework
 * functional attributes component
 *
 * @package earc/functional-attributes
 * @link https://github.com/Koudela/eArc-functional-attributes/
 * @copyright Copyright (c) 2021 Thomas Koudela
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace eArc\FunctionalAttributesTests\env;

use eArc\FunctionalAttributes\Attributes\F;
use eArc\FunctionalAttributes\Attributes\Functional;

class SomeClass
{
    #[Functional([
        F::NOT => [F::OR => []],
    ], ValidatorFactory::class)]
    protected mixed $testCase;

}
