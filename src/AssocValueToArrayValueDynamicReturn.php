<?php declare(strict_types=1);

namespace GW\PHPStan\GwValues;

use GW\Value\AssocValue;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;

final class AssocValueToArrayValueDynamicReturn implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return AssocValue::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'values';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        $type = $scope->getType($methodCall->var);

        if ($type instanceof AssocValueType) {
            return new ArrayValueType($type->innerType());
        }

        return new ArrayValueType(new MixedType());
    }
}
