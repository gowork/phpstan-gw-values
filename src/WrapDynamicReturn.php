<?php declare(strict_types=1);

namespace GW\PHPStan\GwValues;

use GW\Value\Wrap;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;

final class WrapDynamicReturn implements DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return Wrap::class;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'array';
    }

    public function getTypeFromStaticMethodCall(
        MethodReflection $methodReflection,
        StaticCall $methodCall,
        Scope $scope
    ): Type {
        $passedType = $scope->getType($methodCall->args[0]->value);
        $innerType = new MixedType();

        if ($passedType instanceof ArrayType) {
            $innerType = $passedType->getItemType();
        }

        return new ArrayValueType($innerType);
    }
}
