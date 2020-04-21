<?php

declare(strict_types=1);

namespace Setherator\Variables\Tests\Helpers;

use RuntimeException;
use function Setherator\Variables\ref;
use function Setherator\Variables\reference;
use function Setherator\Variables\referenceFn;
use function Setherator\Variables\refFn;
use Setherator\Variables\Tests\Fixtures\StubbedVariables;

/**
 * @author Aurimas Niekis <aurimas@niekis.lt>
 */
class ReferenceHelperTest extends BaseHelperTest
{
    public function testReference(): void
    {
        $this->variables->set('foo', 'bar');

        $this->assertEquals('bar', reference('foo'));
        $this->assertEquals('foo', reference('bar', 'foo'));

        $this->assertEquals('bar', ref('foo'));
        $this->assertEquals('foo', ref('bar', 'foo'));

        $this->assertEquals('bar', referenceFn('foo')());
        $this->assertEquals('foo', referenceFn('bar', 'foo')());

        $this->assertEquals('bar', refFn('foo')());
        $this->assertEquals('foo', refFn('bar', 'foo')());

        StubbedVariables::clearInstance();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Setherator\Variables\Variables was not initialised yet');

        reference('foo');
    }
}
