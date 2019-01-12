<?php declare(strict_types=1);

namespace GW\PHPStan\GwValues;

use GW\Value\ArrayValue;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Type;

final class ArrayValueDynamicReturn implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return ArrayValue::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        if (\in_array($methodReflection->getName(), ['map', 'shuffle'], true)) {
            return false;
        }

        return $methodReflection->getDeclaringClass()->isSubclassOf(ArrayValue::class);
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        /** @var ArrayValueType $type */
        $valueType = $scope->getType($methodCall->var);

        return $valueType;
    }
}
