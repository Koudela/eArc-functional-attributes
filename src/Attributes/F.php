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

use eArc\FunctionalAttributes\Exceptions\MissingCallableException;

abstract class F
{
    const ALL_OF = '~ALL_OF~';
    const ONE_OF = '~ONE_OF~';
    const NONE_OF = '~NONE_OF~';
    const NOT = '~NOT~';
    const AND = '~AND~';
    const OR = '~OR~';
    const XOR = '~XOR~';
    const WHEN = '~WHEN~';

    const FIRST = '~FIRST~';
    const LAST = '~LAST~';
    const EVERY = '~EVERY~';

    const METHODS = [
        F::ALL_OF => 'allOf',
        F::AND => 'allOf',
        F::ONE_OF => 'oneOf',
        F::OR => 'oneOf',
        F::NONE_OF => 'noneOf',
        F::XOR => 'noneOf',
        F::NOT => 'not',
        F::WHEN => 'when',

        F::FIRST => 'first',
        F::LAST => 'last',
        F::EVERY => 'every',
    ];

    /**
     * @throws MissingCallableException
     */
    public function allOf(iterable $callTree): bool
    {
        foreach($callTree as $name => $args) {
            if (!$this->evaluateCallTreeNode($name, $args)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws MissingCallableException
     */
    public function oneOf(iterable $callTree): bool
    {
        foreach($callTree as $name => $args) {
            if ($this->evaluateCallTreeNode($name, $args)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws MissingCallableException
     */
    public function noneOf(iterable $callTree): bool
    {
        foreach($callTree as $name => $args) {
            if ($this->evaluateCallTreeNode($name, $args)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws MissingCallableException
     */
    public function not(iterable $callTree): bool
    {
        return !$this->allOf($callTree);
    }

    /**
     * @throws MissingCallableException
     */
    public function when(iterable $callTree): mixed
    {
        $when = null;

        foreach ($callTree as $name => $args) {
            if (is_null($when)) {
                $when = $this->evaluateCallTreeNode($name, $args) ? 1 : 2;
            } elseif ($when === 1) {
                return $this->evaluateCallTreeNode($name, $args);
            } else {
                $when--;
            }
        }

        return true;
    }

    /**
     * @throws MissingCallableException
     */
    public function first(iterable $callTree): mixed
    {
        $result = null;
        $first = true;

        foreach($callTree as $name => $args) {
            if ($first) {
                $result = $this->evaluateCallTreeNode($name, $args);
                $first = false;
            } else {
                $this->evaluateCallTreeNode($name, $args);
            }
        }

        return $result;

    }

    /**
     * @throws MissingCallableException
     */
    public function last(iterable $callTree): mixed
    {
        $result = null;

        foreach($callTree as $name => $args) {
            $result = $this->evaluateCallTreeNode($name, $args);
        }

        return $result;
    }

    /**
     * @throws MissingCallableException
     */
    public function every(iterable $callTree): bool
    {
        foreach($callTree as $name => $args) {
            $this->evaluateCallTreeNode($name, $args);
        }

        return true;
    }

    /**
     * @throws MissingCallableException
     */
    protected abstract function evaluateCallTreeNode(int|string $name, int|float|string|iterable|null $args): mixed;
}
