<?php declare(strict_types=1);

namespace tests\GW\PHPStan\GwValues\Integration\data;

use GW\Value\Wrap;

final class arrayFromAssoc
{
    public function test(): void
    {
        $this->acceptingArrayOfInts(Wrap::assocArray(['test' => 1, 'x' => 3])->values()->toArray());
    }

    /**
     * @param int[] $ints
     */
    private function acceptingArrayOfInts(array $ints): void
    {
    }
}
