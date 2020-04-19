<?php

declare(strict_types=1);

namespace Setherator\Variables\Tests\Helpers;

use function Setherator\Variables\all;
use function Setherator\Variables\factory;
use function Setherator\Variables\ref;

/**
 * @author Aurimas Niekis <aurimas@niekis.lt>
 */
class AllHelperTest extends BaseHelperTest
{
    public function testAllHelper(): void
    {
        $this->variables->set('foo', 'bar');
        $this->variables->set('bar', 'foo');

        $expected = ['bar', 'foo', 'fooBar', ['bar', 'foo', 'fooBar']];

        $this->assertEquals(
            $expected,
            all(
                fn () => ref('foo'),
                fn () => ref('bar'),
                'fooBar',
                all(
                    fn () => ref('foo'),
                    fn () => ref('bar'),
                    'fooBar'
                )
            )
        );
    }

    public function testFactoryAll(): void
    {
        $this->variables->set('foo', 1);
        $this->variables->set('factory', all(ref('foo')));

        $this->assertEquals([1], $this->variables->get('factory'));
        $this->variables->set('foo', 2);
        $this->assertEquals([1], $this->variables->get('factory'));

        $this->variables->set('foo', 1);
        $this->variables->set('factory', factory(fn () => all(ref('foo'))));

        $this->assertEquals([1], $this->variables->get('factory'));
        $this->variables->set('foo', 2);
        $this->assertEquals([2], $this->variables->get('factory'));
    }
}
