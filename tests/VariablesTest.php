<?php

declare(strict_types=1);

namespace Setherator\Variables\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Setherator\Variables\Tests\Fixtures\StubbedVariables;
use Setherator\Variables\Variables;

/**
 * @author Aurimas Niekis <aurimas@niekis.lt>
 */
class VariablesTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        StubbedVariables::clearInstance();
    }

    public function testInstanceMethods(): void
    {
        $variables = new Variables();

        $this->assertEquals($variables, Variables::getInstance());

        StubbedVariables::clearInstance();

        $variables = new class() extends Variables {
        };

        $this->assertEquals($variables, Variables::getInstance());
    }

    public function testNotInitialised(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Setherator\Variables\Variables was not initialised yet');

        Variables::getInstance();
    }

    public function testAddAndAll(): void
    {
        $variables = new Variables();

        $given = [
            'scalar'  => 'foo',
            'closure' => fn () => 'bar',
        ];

        $expected = [
            'scalar'  => 'foo',
            'closure' => 'bar',
        ];

        $variables->add($given);

        $this->assertEquals($given, $variables->all(false));
        $this->assertEquals($expected, $variables->all());
        $this->assertEquals(['scalar', 'closure'], $variables->names());
    }

    public function testBasicOperations(): void
    {
        $variables = new Variables();

        $this->assertNull($variables->get('foo'));
        $this->assertFalse($variables->has('foo'));

        $variables->set('foo', 'bar');

        $this->assertEquals('bar', $variables->get('foo'));
        $this->assertTrue($variables->has('foo'));

        $variables->remove('foo');

        $this->assertNull($variables->get('foo'));
        $this->assertFalse($variables->has('foo'));
    }
}
