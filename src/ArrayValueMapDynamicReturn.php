<?php declare(strict_types=1);

namespace GW\PHPStan\GwValues;

use GW\Value\ArrayValue;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\CallableType;
use PHPStan\Type\ClosureType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;

final class ArrayValueMapDynamicReturn implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return ArrayValue::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'map';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        /** @var ArrayValueType $type */
        $valueType = $scope->getType($methodCall->var);

        if (\count($methodCall->args) === 0) {
            return new ArrayValueType(new MixedType());
        }

        $innerType = new MixedType();
        $firstAttribute = $methodCall->args[0]->value;
        $callableType = $scope->getType($firstAttribute);

        if ($callableType instanceof ClosureType) {
            $innerType = $callableType->getReturnType();
        }

        if ($callableType instanceof CallableType) {
            $innerType = $callableType->getReturnType() ?? new MixedType();
        }

        return new ArrayValueType($innerType);
    }
}
