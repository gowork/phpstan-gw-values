<?php declare(strict_types=1);

namespace GW\PHPStan\GwValues;

use GW\Value\ArrayValue;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\VerbosityLevel;

final class ArrayValueType extends ObjectType
{
    /** @var Type */
    private $innerType;

    public function __construct(Type $innerType)
    {
        parent::__construct(ArrayValue::class);
        $this->innerType = $innerType;
    }

    public function innerType(): Type
    {
        return $this->innerType;
    }

    public function describe(VerbosityLevel $level): string
    {
        return \sprintf('%s<%s>', parent::describe($level), $this->innerType->describe($level));
    }
}
