<?php

declare(strict_types=1);

namespace Setherator\Variables\Tests\Helpers;

use function Setherator\Variables\factory;
use function Setherator\Variables\logic;
use function Setherator\Variables\ref;

/**
 * @author Aurimas Niekis <aurimas@niekis.lt>
 */
class LogicHelperTest extends BaseHelperTest
{
    public function testLogicHelperLogic(): void
    {
        $ref = 1;

        $logicClosure = logic(
            static function () use (&$ref) {
                return $ref;
            },
            fn () => 'true',
            'false'
        );

        $this->assertEquals('true', $logicClosure());
        $ref = 0;
        $this->assertEquals('false', $logicClosure());

        $logicClosure = logic(
            static function () use (&$ref) {
                return $ref;
            },
            fn () => 'true',
            'false',
            true
        );

        $ref = 1;
        $this->assertEquals('false', $logicClosure());
        $ref = true;
        $this->assertEquals('true', $logicClosure());
    }

    public function testFactoryLogic(): void
    {
        $this->variables->set('ref', 1);
        $this->variables->set(
            'foo',
            logic(
                fn () => ref('ref'),
                fn () => 'true',
                'false'
            )
        );

        $this->assertEquals('true', $this->variables->get('foo'));
        $this->variables->set('ref', 0);
        $this->assertEquals('true', $this->variables->get('foo'));

        $this->variables->set('ref', 1);
        $this->variables->set(
            'foo',
            factory(
                logic(
                    fn () => ref('ref'),
                    fn () => 'true',
                    'false'
                )
            )
        );

        $this->assertEquals('true', $this->variables->get('foo'));
        $this->variables->set('ref', 0);
        $this->assertEquals('false', $this->variables->get('foo'));
    }
}
