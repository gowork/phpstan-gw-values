<?php declare(strict_types=1);

namespace GW\PHPStan\GwValues;

use GW\Value\AssocValue;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\VerbosityLevel;

final class AssocValueType extends ObjectType
{
    /** @var Type */
    private $innerType;

    /** @var Type */
    private $keyType;

    public function __construct(Type $keyType, Type $innerType)
    {
        parent::__construct(AssocValue::class);
        $this->innerType = $innerType;
        $this->keyType = $keyType;
    }

    public function innerType(): Type
    {
        return $this->innerType;
    }

    public function keyType(): Type
    {
        return $this->keyType;
    }

    public function describe(VerbosityLevel $level): string
    {
        return \sprintf('%s<%s>', parent::describe($level), $this->innerType->describe($level));
    }
}
