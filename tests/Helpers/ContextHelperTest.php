<?php

declare(strict_types=1);

namespace Setherator\Variables\Tests\Helpers;

use function Setherator\Variables\context;
use function Setherator\Variables\factory;
use function Setherator\Variables\ref;

/**
 * @author Aurimas Niekis <aurimas@niekis.lt>
 */
class ContextHelperTest extends BaseHelperTest
{
    public function testContextHelper(): void
    {
        $this->variables->setContext(['foo', 'bar']);

        $closure = context(
            static function ($foo, $bar, $fooBar) {
                return [$foo, $bar, $fooBar];
            },
            fn () => 'fooBar',
        );

        $this->assertEquals(['foo', 'bar', 'fooBar'], $closure());
    }

    public function testFactoryContext(): void
    {
        $this->variables->setContext(['foo', 'bar']);

        $this->variables->set('foo', 1);
        $this->variables->set(
            'factory',
                context(
                    static function ($foo, $bar, $fooBar) {
                        return [$foo, $bar, $fooBar];
                    },
                    fn () => ref('foo')
                )
        );

        $this->assertEquals(['foo', 'bar', 1], $this->variables->get('factory'));
        $this->variables->set('foo', 2);
        $this->assertEquals(['foo', 'bar', 1], $this->variables->get('factory'));

        $this->variables->set('foo', 1);
        $this->variables->set(
            'factory',
            factory(
                context(
                    static function ($foo, $bar, $fooBar) {
                        return [$foo, $bar, $fooBar];
                    },
                    fn () => ref('foo')
                )
            )
        );

        $this->assertEquals(['foo', 'bar', 1], $this->variables->get('factory'));
        $this->variables->set('foo', 2);
        $this->assertEquals(['foo', 'bar', 2], $this->variables->get('factory'));
    }
}
