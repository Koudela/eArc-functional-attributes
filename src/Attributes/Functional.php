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

namespace eArc\FunctionalAttributes\Attributes;

use Attribute;
use eArc\DI\Exceptions\MakeClassException;
use eArc\FunctionalAttributes\Exceptions\MissingCallableException;
use eArc\FunctionalAttributes\Interfaces\ServiceInterface;

#[Attribute]
class Functional extends F
{
    protected object|null $fallback = null;
    protected mixed $appliedArgument;

    public function __construct(
        protected iterable $callTree,
        string|null $fallbackFQCN,
    ) {
        if (is_string($fallbackFQCN)) {
            $this->fallback = di_get($fallbackFQCN);
        }
    }


    /**
     * @throws MissingCallableException
     */
    public function evaluate(mixed $appliedArgument): bool
    {
        $this->appliedArgument = $appliedArgument;

        return $this->allOf($this->callTree);
    }

    /**
     * @throws MissingCallableException
     */
    protected function evaluateCallTreeNode(int|string $name, int|float|string|iterable|null $args): mixed
    {
        if (is_string($name)) {
            return static::evaluateCallTreeNodeWithArguments($name, $args);
        }

        if (is_string($args)) {
            return static::evaluateCallTreeNodeWithArguments($name, null);
        }

        if (is_iterable($args)) {
            return static::evaluateCallTreeNodeWithArguments(F::AND, $args);
        }

        return $args;
    }

    /**
     * @throws MissingCallableException
     */
    protected function evaluateCallTreeNodeWithArguments(string $name, int|float|string|iterable|null $args): mixed
    {

        $parts = explode('::', $name, 2);

        if (2 === count($parts)) {
            return static::evaluateCallTreeMethod($parts[0], $parts[1], $args);
        }

        if (array_key_exists($name, static::METHODS) && is_iterable($args)) {
            $method = static::METHODS[$name];
            static::$method($args);
        }

        if ($this->fallback instanceof ServiceInterface) {
            return $this->fallback->callFunctional($name, $args, $this->appliedArgument);
        }

        throw new MissingCallableException(sprintf(
            '{69cd8f7f-5684-4b83-b1d5-da60a79b44cd} `%s` does not relate to a callable.',
            $name
        ));
    }

    /**
     * @throws MissingCallableException
     */
    protected function evaluateCallTreeMethod(string $className, string $methodName, int|string|array|null $args): mixed
    {
        if (method_exists($className, $methodName)) {
            try {
                return is_array($args) ? di_get($className)->$methodName($this->appliedArgument, ...$args)
                    : di_get($className)->$methodName($this->appliedArgument, $args);
            } catch (MakeClassException) {
                return is_array($args) ? di_static($className)::$methodName($this->appliedArgument, ...$args)
                    : di_static($className)::$methodName($this->appliedArgument, $args);
            }
        }

        if ($this->fallback instanceof ServiceInterface) {
            return $this->fallback->callFunctional($className.'::'.$methodName, $args, $this->appliedArgument);
        }

        throw new MissingCallableException(sprintf(
            '{e15d9366-56e7-497f-bd5c-8d66994b628f} `%s::%s` does not relate to a callable.',
            $className,
            $methodName
        ));
    }
}
