<?php

/**********************************************************************************************************************
 * phpLINQ (https://github.com/mkloubert/phpLINQ)                                                                     *
 *                                                                                                                    *
 * Copyright (c) 2015, Marcel Joachim Kloubert <marcel.kloubert@gmx.net>                                              *
 * All rights reserved.                                                                                               *
 *                                                                                                                    *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the   *
 * following conditions are met:                                                                                      *
 *                                                                                                                    *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the          *
 *    following disclaimer.                                                                                           *
 *                                                                                                                    *
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the       *
 *    following disclaimer in the documentation and/or other materials provided with the distribution.                *
 *                                                                                                                    *
 * 3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote    *
 *    products derived from this software without specific prior written permission.                                  *
 *                                                                                                                    *
 *                                                                                                                    *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, *
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE  *
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, *
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR    *
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,  *
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE   *
 * USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.                                           *
 *                                                                                                                    *
 **********************************************************************************************************************/

namespace System;


/**
 * An object.
 *
 * @package System
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Object extends \phpLINQ implements IObject {
    /**
     * {@inheritDoc}
     */
    public final function __toString() {
        return $this->toString()
                    ->getWrappedValue();
    }


    /**
     * Returns a value as callable.
     *
     * @param mixed $val The input value.
     *
     * @return callable|null The output value.
     *
     * @throws ArgumentException $val is invalid.
     */
    public static function asCallable($val) {
        $val = static::getRealValue($val);

        if (\is_callable($val) || (null === $val)) {
            return $val;
        }

        return static::toLambda($val);
    }

    /**
     * {@inheritDoc}
     */
    public function cloneMe() : ICloneable {
        return clone $this;
    }

    /**
     * Converts a value to another type.
     *
     * @param mixed $val The value to convert.
     * @param string|\ReflectionClass $conversionType The type the object should be converted to.
     * @param IFormatProvider|null $provider The optional format provider to use.
     * @param bool $handleConvertible Handle IConvertible objects or not.
     *
     * @return mixed The new object / value.
     *
     * @throws InvalidCastException Conversion failed.
     */
    public static function convertTo($val, $conversionType, IFormatProvider $provider = null,
                                     $handleConvertible = true) {
        $conversionType = static::getRealValue($conversionType);
        $valueToConvert = static::getRealValue($val);

        if (($valueToConvert instanceof IConvertible) && $handleConvertible) {
            return $valueToConvert->toType($conversionType, $provider);
        }

        $thisCls = new \ReflectionClass(static::class);
        $acm     = $thisCls->getMethod('asCallable')->getClosure(null);
        $icm     = $thisCls->getMethod('isCallable')->getClosure(null);

        $valueToCallable = function() use ($acm, $icm, &$valueToConvert) {
            if (!$icm($valueToConvert)) {
                return function() use (&$valueToConvert) {
                    return $valueToConvert;
                };
            }

            return $acm($valueToConvert);
        };

        if (\is_object($conversionType)) {
            if (!$conversionType instanceof \ReflectionClass) {
                $conversionType = new \ReflectionObject($conversionType);
            }
        }

        $typeName = \trim(ClrString::valueToString($conversionType));

        if (!$conversionType instanceof \ReflectionClass) {
            if (\class_exists($typeName) || \interface_exists($typeName)) {
                $conversionType = new \ReflectionClass($typeName);
            }
        }

        if ($conversionType instanceof \ReflectionClass) {
            if (\is_object($valueToConvert) &&
                $conversionType->isInstance($valueToConvert)) {

                return $valueToConvert;
            }

            // to closure?
            if ($conversionType->getName() === \Closure::class) {
                return function() use ($valueToCallable) {
                    return \call_user_func_array($valueToCallable(),
                                                 \func_get_args());
                };
            }

            // to IValueWrapper?
            if ($conversionType->isInterface() &&
                ($conversionType->getName() === IValueWrapper::class)) {

                return new Comparer($valueToConvert);
            }

            // to ILazy?
            if ($conversionType->isInterface() &&
                ($conversionType->getName() === ILazy::class)) {

                return new Lazy($valueToCallable());
            }

            // ILazy based object?
            if ($conversionType->isInstantiable() &&
                $conversionType->implementsInterface(ILazy::class)) {

                return $conversionType->newInstance($valueToCallable());
            }

            // IValueWrapper based object?
            if ($conversionType->isInstantiable() &&
                $conversionType->implementsInterface(IValueWrapper::class)) {

                return $conversionType->newInstance($valueToConvert);
            }

            if (null === $valueToConvert) {
                return null;
            }
        }
        else {
            switch ($typeName) {
                case 'callable':
                case 'lazy':
                case 'function':
                    return 'lazy' === $typeName ? new Lazy($valueToCallable())
                                                : $valueToCallable();

                case 'real':
                    return (float)$valueToConvert;

                case 'unset':
                    return null;
            }

            if (false !== \settype($valueToConvert, $typeName)) {
                return $valueToConvert;
            }
        }

        throw new InvalidCastException($conversionType, $val);
    }

    /**
     * {@inheritDoc}
     */
    public function equals($other) : bool {
        return $this == static::getRealValue($other);
    }

    /**
     * Keeps sure that a comparer function is NOT (null).
     *
     * @param callable $comparer The input value.
     *
     * @return callable The output value.
     *
     * @throws ArgumentException $comparer is no valid callable / lambda expression.
     */
    public static function getComparerSafe($comparer) : callable {
        $comparer = static::asCallable($comparer);

        $defaultComparer = function($x, $y) : int {
            if ($x instanceof IComparable) {
                return $x->compareTo($y);
            }
            else if ($x instanceof IEquatable) {
                if ($x->equals($y)) {
                    return 0;
                }
            }

            if ($y instanceof IComparable) {
                $yToXComparer = new DescendingComparer($y);

                return $yToXComparer->compareTo($x);
            }
            else if ($y instanceof IEquatable) {
                if ($y->equals($x)) {
                    return 0;
                }
            }

            if ($x > $y) {
                return 1;
            }
            else if ($x < $y) {
                return -1;
            }

            return 0;
        };

        if (null === $comparer) {
            // default
            return $defaultComparer;
        }

        $rf = static::toReflectionFunction($comparer);
        if ($rf->getNumberOfParameters() < 2) {
            // use function as selector

            return function($x, $y) use ($defaultComparer, $comparer) : int {
                return $defaultComparer($comparer($x),
                                        $comparer($y));
            };
        }

        return static::wrapComparer($comparer);
    }

    /**
     * Keeps sure that a converter is NOT (null).
     *
     * @param callable $converter The input value.
     *
     * @return callable The output value.
     *
     * @throws ArgumentException $converter is no valid callable / lambda expression.
     */
    public static function getConverterSafe($converter) : callable {
        $converter = static::asCallable($converter);

        if (null === $converter) {
            $cls = new \ReflectionClass(static::class);

            return $cls->getMethod('convertTo')
                       ->getClosure(null);
        }

        return static::wrapConverter($converter);
    }

    /**
     * Keeps sure that a equality comparer is NOT (null).
     *
     * @param callable $equalityComparer The input value.
     *
     * @return callable The output value.
     *
     * @throws ArgumentException $equalityComparer is no valid callable / lambda expression.
     */
    public static function getEqualityComparerSafe($equalityComparer) : callable {
        if (true === $equalityComparer) {
            $equalityComparer = function($x, $y) : bool {
                return $x === $y;
            };
        }

        $equalityComparer = static::asCallable($equalityComparer);

        $defaultEqualityComparer = function($x, $y) : bool {
            if ($x instanceof IEquatable) {
                return $x->equals($y);
            }
            else if ($y instanceof IEquatable) {
                return $y->equals($x);
            }

            return $x == $y;
        };

        if (null === $equalityComparer) {
            // default
            return $defaultEqualityComparer;
        }

        $rf = static::toReflectionFunction($equalityComparer);
        if ($rf->getNumberOfParameters() < 2) {
            // use function as selector

            return function($x, $y) use ($defaultEqualityComparer, $equalityComparer) : bool {
                return $defaultEqualityComparer($equalityComparer($x),
                                                $equalityComparer($y));
            };
        }

        return static::wrapEqualityComparer($equalityComparer);
    }

    /**
     * Keeps sure that a predicate function is NOT (null).
     *
     * @param callable $predicate The input value.
     *
     * @return callable The output value.
     *
     * @throws ArgumentException $predicate is no valid callable / lambda expression.
     */
    public static function getPredicateSafe($predicate) : callable {
        if (null === $predicate) {
            // default

            return function() : bool {
                return true;
            };
        }

        return static::wrapPredicate($predicate);
    }

    /**
     * Extracts the "real" value if needed.
     *
     * @param mixed $val The input value.
     *
     * @return mixed The output value.
     */
    public static function getRealValue($val) {
        $handledValues = [];

        $checkForUnhandledValue = function($valueToCheck) use (&$handledValues) {
            foreach ($handledValues as $hv) {
                if ($hv === $valueToCheck) {
                    // already handled
                    return false;
                }
            }

            // not handled yet => add to list
            $handledValues[] = $valueToCheck;
            return true;
        };

        while (true) {
            if ($val instanceof IValueWrapper) {
                $wrappedValue = $val->getWrappedValue();

                if ($wrappedValue !== $val) {
                    if ($checkForUnhandledValue($wrappedValue)) {
                        $val = $wrappedValue;
                        continue;  // check next
                    }
                }
            }

            if ($val instanceof ILazy) {
                $lazyVal = $val->value();

                if ($lazyVal !== $val) {
                    if ($checkForUnhandledValue($lazyVal)) {
                        $val = $lazyVal;
                        continue;  // check next
                    }
                }
            }

            break;  // no infinite loop
        };

        return $val;
    }

    /**
     * {@inheritDoc}
     */
    public final function getType() : \ReflectionObject {
        return new \ReflectionObject($this);
    }

    /**
     * Keeps sure that an value validator function is NOT (null).
     *
     * @param callable $validator The input value.
     *
     * @return callable The output value.
     */
    public static function getValueValidatorSafe($validator) : callable {
        if (null === $validator) {
            // default

            return function() {
                return true;
            };
        }

        return static::wrapValueValidator($validator);
    }

    /**
     * Checks if a value can be executed / is callable.
     *
     * @param mixed $val The value to check.
     *
     * @return bool Can be executed or not.
     */
    public static function isCallable($val) {
        $val = static::getRealValue($val);

        return \is_callable($val) ||
               static::isLambda($val);
    }

    /**
     * A default random value seeder.
     */
    public static function seedRandom() {
        list($usec, $sec) = \explode(' ', \microtime());
        $seed = (float)$sec + ((float)$usec * 100000);

        \mt_srand((int)$seed);
    }

    /**
     * Creates a reflector object for a callable.
     *
     * @param mixed $func The callable.
     *
     * @return \ReflectionFunctionAbstract The created reflector.
     */
    public static function toReflectionFunction($func) : \ReflectionFunctionAbstract {
        if (\is_object($func)) {
            if (\method_exists($func, '__invoke')) {
                $func = [$func, '__invoke'];
            }
        }

        if (\is_array($func)) {
            return new \ReflectionMethod($func[0], $func[1]);
        }

        return new \ReflectionFunction($func);
    }

    /**
     * {@inheritDoc}
     */
    public function toString() : IString {
        return new ClrString(\get_class($this));
    }

    /**
     * {@inheritDoc}
     */
    public function toType($conversionType, IFormatProvider $provider = null) {
        return static::convertTo($this,
                                 $conversionType, $provider,
                                 false);
    }

    /**
     * Invokes a function for a disposable object and keeps sure that the
     * IDisposable::dispose() method of it is called at the end of the invocation, even
     * if an exception is thrown. s. https://msdn.microsoft.com/en-us/library/yh598w02.aspx
     *
     * @param callable $func The function to invoke.
     * @param IDisposable $obj The disposable object.
     * @param mixed ...$arg One or more additional argument for $func.
     *
     * @return mixed The result of $func.
     *
     * @throws ArgumentException $func is no valid callable / lambda expression.
     * @throws ArgumentNullException $func is (null).
     */
    public static function using($func, IDisposable $obj = null) {
        if (null === $func) {
            throw new ArgumentNullException('func');
        }

        $func = static::asCallable($func);

        try {
            return \call_user_func_array($func,
                                         \array_merge([$obj],
                                                      \array_slice(\func_get_args(), 2)));
        }
        finally {
            if (null !== $obj) {
                $obj->dispose();
            }
        }
    }

    /**
     * Wraps a comparer with a callable that requires an integer as result value.
     *
     * @param callable $comparer The equality comparer to wrap.
     *
     * @return callable The wrapper.
     *
     * @throws ArgumentException $comparer is no valid callable / lambda expression.
     */
    public static function wrapComparer($comparer) : callable {
        $comparer = static::asCallable($comparer);

        return function($x, $y) use ($comparer) : int {
            return $comparer($x, $y);
        };
    }

    /**
     * Wraps a value converter with a safe callable.
     *
     * @param callable $converter The converter to wrap.
     *
     * @return callable The wrapper.
     *
     * @throws ArgumentException $converter is no valid callable / lambda expression.
     */
    public static function wrapConverter($converter) : callable {
        $converter = static::asCallable($converter);

        return function($val, $conversionType, IFormatProvider $provider = null) use ($converter) {
            return $converter($val, $conversionType, $provider);
        };
    }

    /**
     * Wraps an equality comparer with a callable that requires a boolean as result value.
     *
     * @param callable $equalityComparer The equality comparer to wrap.
     *
     * @return callable The wrapper.
     *
     * @throws ArgumentException $equalityComparer is no valid callable / lambda expression.
     */
    public static function wrapEqualityComparer($equalityComparer) : callable {
        $equalityComparer = static::asCallable($equalityComparer);

        return function($x, $y) use ($equalityComparer) : bool {
            return $equalityComparer($x, $y);
        };
    }

    /**
     * Wraps a predicate with a callable that requires a boolean as result value.
     *
     * @param callable $predicate The predicate to wrap.
     *
     * @return callable The wrapper.
     *
     * @throws ArgumentException $predicate is no valid callable / lambda expression.
     */
    public static function wrapPredicate($predicate) : callable {
        $predicate = static::asCallable($predicate);

        return function($x, $ctx) use ($predicate) : bool {
            return $predicate($x, $ctx);
        };
    }

    /**
     * Wraps a value validator with a callable that requires a boolean as result value.
     *
     * @param callable $validator The validator to wrap.
     *
     * @return callable The wrapper.
     *
     * @throws ArgumentException $validator is no valid callable / lambda expression.
     */
    public static function wrapValueValidator($validator) : callable {
        $validator = static::asCallable($validator);

        return function($x) use ($validator) : bool {
            return $validator($x);
        };
    }
}
