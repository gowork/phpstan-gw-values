<?php declare(strict_types=1);

namespace tests\GW\PHPStan\GwValues\Integration;

use GW\Value\Wrap;

final class Example
{
    public function test()
    {
        $array = Wrap::array([1, 3, 4]);

        $strings = $array->map(
            function (int $n): string {
                return "n{$n}";
            }
        )->toArray();

        $n = $strings[0] + 2;
    }
}
