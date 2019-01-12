<?php declare(strict_types=1);

namespace GW\PHPStan\GwValues;

use GW\Value\ArrayValue;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\IterableType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;

final class TypeHelper
{
    public static function searchArrayValueType(Type $type): ?ArrayValueType
    {
        if ($type instanceof ArrayValueType) {
            return $type;
        }

        if ($type instanceof IntersectionType) {
            $subTypes = $type->getTypes();

            if (\count($subTypes) === 2
                && $subTypes[0] instanceof ObjectType
                && $subTypes[1] instanceof IterableType
                && $subTypes[0]->isSuperTypeOf(new ObjectType(ArrayValue::class))) {
                return new ArrayValueType($subTypes[1]->getIterableValueType());
            }
        }

        if ($type instanceof IntersectionType || $type instanceof UnionType) {
            foreach ($type->getTypes() as $subType) {
                $try = self::searchArrayValueType($subType);

                if ($try) {
                    return $try;
                }
            }
        }

        return null;
    }
}
