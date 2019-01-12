<?php declare(strict_types=1);

namespace tests\GW\PHPStan\GwValues\Integration;

use GW\Value\Wrap;

final class Example
{
    public function test()
    {
        $ints = Wrap::array([1, 3, 4]);

        $strings = $ints->map(
            function (int $n): string {
                return "n{$n}";
            }
        );

        chr($ints->first());
        chr($strings->first());

        $n = $strings->toArray()[0] + 2;

        $ints->map(
            function (string $s): int {
                return 2;
            }
        );

        $ints
            ->map(
                function (int $s): int {
                    return 2;
                }
            )
            ->map(
                function (int $n): string {
                    return "n{$n}";
                }
            )
            ->map(
                function (int $s): int {
                    return 2;
                }
            );

        $this->acceptingArrayOfInts($ints->chunk(2)->toArray());
        $this->acceptingArrayOfInts($ints->chunk(2)->toArray()[0]);
    }

    /**
     * @param int[] $ints
     */
    private function acceptingArrayOfInts(array $ints): void
    {
    }
}
