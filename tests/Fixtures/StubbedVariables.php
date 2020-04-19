<?php

declare(strict_types=1);

namespace Setherator\Variables\Tests\Fixtures;

use Setherator\Variables\Variables;

/**
 * @author Aurimas Niekis <aurimas@niekis.lt>
 */
class StubbedVariables extends Variables
{
    public static function clearInstance(): void
    {
        static::$instance = null;
    }
}
