<?php

declare(strict_types=1);

namespace Setherator\Variables;

use Closure;

/**
 * @author Aurimas Niekis <aurimas@niekis.lt>
 */
class NonCacheableClosure
{
    private Closure $closure;

    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    public function __invoke()
    {
        return $this->closure->call(new class() {
        });
    }
}
