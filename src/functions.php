<?php

declare(strict_types=1);

namespace Setherator\Variables;

use Closure;

function env(string $name, $default = null)
{
    $result = getenv($name);

    return false !== $result ? $result : $default;
}

function ref(string $name, $default = null, bool $raw = false)
{
    return reference($name, $default, $raw);
}

function refFn(string $name, $default = null, bool $raw = false)
{
    return fn () => ref($name, $default, $raw);
}

function reference(string $name, $default = null, bool $raw = false)
{
    if ($raw) {
        return Variables::getInstance()->getRaw($name, $default);
    }

    return Variables::getInstance()->get($name, $default);
}

function referenceFn(string $name, $default = null, bool $raw = false)
{
    return fn () => reference($name, $default, $raw);
}

function context(Closure $closure, ...$args): Closure
{
    return fn () => $closure(...Variables::getInstance()->getContext(), ...all(...$args));
}

function factory(Closure $closure, ...$args): NonCacheableClosure
{
    return new NonCacheableClosure(fn () => $closure(...all(...$args)));
}

function all(...$args): array
{
    $result = [];
    foreach ($args as $arg) {
        $result[] = Variables::getInstance()->parseValue($arg);
    }

    return $result;
}

function allFn(...$args): Closure
{
    return fn () => all(...$args);
}

function logic($condition, $true, $false, bool $strict = false): Closure
{
    return static function () use ($condition, $true, $false, $strict) {
        $condition = Variables::getInstance()->parseValue($condition);

        if ($strict) {
            if (true === $condition) {
                return Variables::getInstance()->parseValue($true);
            }

            return Variables::getInstance()->parseValue($false);
        }

        if ($condition) {
            return Variables::getInstance()->parseValue($true);
        }

        return Variables::getInstance()->parseValue($false);
    };
}

function passthrough(Closure $closure, ...$args): Closure
{
    return fn () => $closure(...all(...$args));
}

function first(...$args)
{
    foreach ($args as $value) {
        $value = Variables::getInstance()->parseValue($value);

        if ($value) {
            return $value;
        }
    }

    return null;
}

function firstFn(...$args): Closure
{
    return fn () => first(...$args);
}
