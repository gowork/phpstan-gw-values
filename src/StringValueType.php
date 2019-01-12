<?php declare(strict_types=1);

namespace GW\PHPStan\GwValues;

use GW\Value\StringValue;
use PHPStan\Type\ObjectType;

final class StringValueType extends ObjectType
{
    public function __construct()
    {
        parent::__construct(StringValue::class);
    }
}
