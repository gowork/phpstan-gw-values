<?php declare(strict_types=1);

namespace GW\PHPStan\GwValues;

use GW\Value\Wrap;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
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
        /** @var ArrayType $innerType */
        $innerType = $scope->getType($methodCall->args[0]->value);

        return new ArrayValueType($innerType->getItemType());
    }
}
