<?php

declare(strict_types=1);

namespace Setherator\Variables\Tests\Helpers;

use function Setherator\Variables\factory;
use function Setherator\Variables\passthrough;
use function Setherator\Variables\ref;

/**
 * @author Aurimas Niekis <aurimas@niekis.lt>
 */
class PassthroughHelperTest extends BaseHelperTest
{
    public function testPassthroughHelper(): void
    {
        $this->variables->set('foo', 'bar');

        $this->assertEquals(
            'bar',
            passthrough(
                fn ($bar) => $bar,
                fn ()     => ref('foo')
            )()
        );
    }

    public function testFactoryPassthrough(): void
    {
        $this->variables->set('ref', 1);
        $this->variables->set(
            'factory',
            passthrough(
                fn ($ref) => $ref,
                fn ()     => ref('ref')
            )
        );

        $this->assertEquals(1, $this->variables->get('factory'));
        $this->variables->set('ref', 2);
        $this->assertEquals(1, $this->variables->get('factory'));

        $this->variables->set('ref', 1);
        $this->variables->set(
            'factory',
            factory(
                passthrough(
                    fn ($ref) => $ref,
                    fn ()     => ref('ref')
                )
            )
        );

        $this->assertEquals(1, $this->variables->get('factory'));
        $this->variables->set('ref', 2);
        $this->assertEquals(2, $this->variables->get('factory'));
    }
}
