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

namespace eArc\FunctionalAttributes;

use eArc\FunctionalAttributes\Attributes\Functional;
use eArc\FunctionalAttributes\Exceptions\MissingCallableException;
use ReflectionAttribute;
use ReflectionProperty;

class EvaluationService
{
    public function __construct(
        protected string $attributeClass = Functional::class
    ) {}

    /**
     * @throws MissingCallableException
     */
    public function evaluateProperty(ReflectionProperty $property): array|bool|null
    {
        $attributes = $property->getAttributes($this->attributeClass);
        $attribute = array_pop($attributes);

        if ($attribute instanceof ReflectionAttribute) {
            /** @var Functional $functional */
            $functional = $attribute->newInstance();
            $property->setAccessible(true);

            return $functional->evaluate($property);
        }

        return null;
    }

    /**
     * @throws MissingCallableException
     */
    public function evaluateValue(mixed $value, iterable $callTree, string|null $fallbackFQCN): bool
    {
        return (new ($this->attributeClass)($callTree, $fallbackFQCN))->evaluate($value);
    }
}
