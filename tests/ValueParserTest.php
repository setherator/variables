<?php

declare(strict_types=1);

namespace Setherator\Variables\Tests;

use PHPUnit\Framework\TestCase;
use Setherator\Variables\ValueParserInterface;
use Setherator\Variables\Variables;
use stdClass;

/**
 * @author Aurimas Niekis <aurimas@niekis.lt>
 */
class ValueParserTest extends TestCase
{
    public function testValueParser(): void
    {
        $variables   = new Variables();
        $valueParser = new class() implements ValueParserInterface {
            public function supports($value): bool
            {
                return $value instanceof stdClass;
            }

            public function parseValue($value, Variables $variables)
            {
                return json_encode($value);
            }
        };

        $variables->addValueParser($valueParser);

        $stdClass      = new stdClass();
        $stdClass->foo = 'bar';

        $variables->set('stdClass', $stdClass);

        $this->assertEquals('{"foo":"bar"}', $variables->get('stdClass'));
    }
}
