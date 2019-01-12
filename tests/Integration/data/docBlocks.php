<?php declare(strict_types=1);

namespace tests\GW\PHPStan\GwValues\Integration;

use GW\Value\ArrayValue;
use GW\Value\Wrap;

class Label {}

final class docBlocks
{
    /** @var ArrayValue|Label[] */
    private $items;

    public function __construct(Label ...$items)
    {
        $this->items = Wrap::array($items);
    }

    public function test(): void
    {
        $this->items->map(function (Label $x): string { return ''; });
        $this->items->map(function (int $x): string { return ''; });

        $this->acceptingArrayOfLabels($this->items->toArray());
    }

    /**
     * @param Label[] $items
     */
    private function acceptingArrayOfLabels(array $items): void
    {
    }
}
