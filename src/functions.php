<?php

declare(strict_types=1);

namespace Setherator\Variables;

use Closure;

function env(string $name, $default = null)
{
    $result = getenv($name);

    return false !== $result ? $result : $default;
}

function ref(string $name, $default = null)
{
    return reference($name, $default);
}

function reference(string $name, $default = null)
{
    return Variables::getInstance()->get($name, $default);
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
