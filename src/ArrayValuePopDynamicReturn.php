<?php declare(strict_types=1);

namespace GW\PHPStan\GwValues;

use GW\Value\ArrayValue;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\NullType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;

final class ArrayValuePopDynamicReturn implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return ArrayValue::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return \in_array($methodReflection->getName(), ['pop', 'offsetGet', 'first', 'last', 'find', 'findLast'], true);
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        /** @var ArrayValueType $type */
        $type = $scope->getType($methodCall->var);
        $innerType = $type->innerType();

        if ($innerType instanceof UnionType) {
            return new UnionType(\array_merge([new NullType()], $innerType->getTypes()));
        }

        return new UnionType([new NullType(), $innerType]);
    }
}
