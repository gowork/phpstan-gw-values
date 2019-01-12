<?php declare(strict_types=1);

namespace GW\PHPStan\GwValues;

use GW\Value\ArrayValue;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\IntegerType;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;

final class ArrayValueToArrayDynamicReturn implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return ArrayValue::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return \in_array($methodReflection->getName(), ['toArray', 'getIterator'], true);
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        $type = $scope->getType($methodCall->var);

        return $this->fromType($type) ?? new ArrayType(new IntegerType(), new MixedType());
    }

    private function fromType(Type $type): ?ArrayType
    {
        if ($type instanceof ArrayValueType) {
            return new ArrayType(new IntegerType(), $type->innerType());
        }

        if ($type instanceof IntersectionType || $type instanceof UnionType) {
            foreach ($type->getTypes() as $subType) {
                $try = $this->fromType($subType);

                if ($try) {
                    return $try;
                }
            }
        }

        return null;
    }
}
