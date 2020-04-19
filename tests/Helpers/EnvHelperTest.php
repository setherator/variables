<?php

declare(strict_types=1);

namespace Setherator\Variables\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use function Setherator\Variables\env;

/**
 * @author Aurimas Niekis <aurimas@niekis.lt>
 */
class EnvHelperTest extends TestCase
{
    public function testEnvHelper(): void
    {
        $this->assertEquals('FOO', env('SETHERATOR_ENV_VAR'));
        $this->assertEquals('default', env(md5('SUPER_RANDOM'), 'default'));
    }
}
