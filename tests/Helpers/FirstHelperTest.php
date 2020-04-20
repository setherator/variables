<?php

declare(strict_types=1);

namespace Setherator\Variables\Tests\Helpers;

use function Setherator\Variables\first;

/**
 * @author Aurimas Niekis <aurimas@niekis.lt>
 */
class FirstHelperTest extends BaseHelperTest
{
    public function testFirstHelper(): void
    {
        $this->assertEquals(
            'foo',
            first(
                'foo',
                'bar',
                1
            )
        );

        $this->assertEquals(
            'bar',
            first(
                0,
                'bar',
                1
            )
        );

        $this->assertNull(
            first(
                0,
                null,
                false,
                ''
            )
        );
    }
}
