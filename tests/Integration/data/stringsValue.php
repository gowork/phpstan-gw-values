<?php declare(strict_types=1);

namespace tests\GW\PHPStan\GwValues\Integration\data;

use GW\Value\StringValue;
use GW\Value\Wrap;

final class stringsValue
{
    public function test(): void
    {
        Wrap::stringsArray(['test', 'x'])
            ->map(function (StringValue $value): StringValue { return $value; })
            ->trim();

        Wrap::string('test')
            ->trim();
    }
}
