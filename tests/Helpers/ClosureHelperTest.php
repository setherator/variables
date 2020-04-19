<?php

declare(strict_types=1);

namespace Setherator\Variables\Tests\Helpers;

/**
 * @author Aurimas Niekis <aurimas@niekis.lt>
 */
class ClosureHelperTest extends BaseHelperTest
{
    public function testClosures(): void
    {
        $value = 'bar';

        $this->variables->set('foo', fn () => $value);

        $this->assertEquals($value, $this->variables->get('foo'));

        // Make sure that value is cached;

        $value = 'foo';
        $this->assertEquals('bar', $this->variables->get('foo'));
    }
}
