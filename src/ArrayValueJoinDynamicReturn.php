<?php declare(strict_types=1);

namespace GW\PHPStan\GwValues;

use GW\Value\ArrayValue;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;

final class ArrayValueJoinDynamicReturn implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return ArrayValue::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'join';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        $type = TypeHelper::searchArrayValueType($scope->getType($methodCall->var));

        $parameter = $methodCall->args[0]->value;

        /** @var ArrayValueType $parameterType */
        $parameterType = $scope->getType($parameter);

        return new ArrayValueType(
            new UnionType([$type ? $type->innerType() : new MixedType(), $parameterType->innerType()])
        );
    }
}
