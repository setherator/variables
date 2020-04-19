<?php

declare(strict_types=1);

namespace Setherator\Variables\Tests\Helpers;

use function Setherator\Variables\factory;

/**
 * @author Aurimas Niekis <aurimas@niekis.lt>
 */
class FactoryHelperTest extends BaseHelperTest
{
    public function testFactoryWrapsCorrectly(): void
    {
        $ref = 1;

        $closure = fn (...$args) => $args;

        $wrappedClosure = factory($closure, 'foo', static function () use (&$ref) { return $ref; });

        $this->assertEquals(['foo', 1], $wrappedClosure());

        $ref = 2;

        $this->assertEquals(['foo', 2], $wrappedClosure());
    }

    public function testInVariables(): void
    {
        $ref = 1;

        $this->variables->set('foo', factory(
            static function () use (&$ref) {
                return $ref;
            }
        ));

        $this->assertEquals(1, $this->variables->get('foo'));

        $ref = 2;

        $this->assertEquals(2, $this->variables->get('foo'));
    }
}
