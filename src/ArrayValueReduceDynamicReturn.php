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

final class ArrayValueReduceDynamicReturn implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return ArrayValue::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'reduce';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        if (\count($methodCall->args) === 0) {
            return new MixedType();
        }

        $firstAttribute = $methodCall->args[0]->value;
        $callableType = $scope->getType($firstAttribute);

        if ($callableType instanceof ClosureType) {
            return $callableType->getReturnType();
        }

        if ($callableType instanceof CallableType) {
            return $callableType->getReturnType() ?? new MixedType();
        }

        $type = TypeHelper::searchArrayValueType($scope->getType($methodCall->var));

        if ($type) {
            return $type->innerType();
        }

        return new MixedType();
    }
}
