<?php

declare(strict_types=1);

namespace Setherator\Variables\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use Setherator\Variables\Tests\Fixtures\StubbedVariables;
use Setherator\Variables\Variables;

/**
 * @author Aurimas Niekis <aurimas@niekis.lt>
 */
abstract class BaseHelperTest extends TestCase
{
    protected Variables $variables;

    protected function setUp(): void
    {
        parent::setUp();

        $this->variables = new Variables();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        StubbedVariables::clearInstance();
    }
}
