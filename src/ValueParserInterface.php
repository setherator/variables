<?php

declare(strict_types=1);

namespace Setherator\Variables;

/**
 * @author Aurimas Niekis <aurimas@niekis.lt>
 */
interface ValueParserInterface
{
    public function supports($value): bool;

    public function parseValue($value, Variables $variables);
}
