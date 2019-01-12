<?php declare(strict_types=1);

namespace GW\PHPStan\GwValues;

use GW\Value\ArrayValue;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ArrayType;
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
        return
            ($methodReflection->getDeclaringClass()->getName() === ArrayValue::class
                || $methodReflection->getDeclaringClass()->isSubclassOf(
                    ArrayValue::class
                ))
            && \in_array(
                $methodReflection->getName(),
                ['map', 'flatMap'],
                true
            );
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        $valueType = TypeHelper::searchArrayValueType($scope->getType($methodCall->var));

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

        if ($methodReflection->getName() === 'flatMap' && !$innerType instanceof MixedType) {
            if (!$innerType instanceof ArrayType) {
                throw new ShouldNotHappenException('flatMap callback must return array');
            }

            $innerType = $innerType->getItemType();
        }

        return new ArrayValueType($innerType, $methodReflection->getDeclaringClass()->getName());
    }
}
