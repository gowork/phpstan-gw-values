<?php declare(strict_types=1);

namespace GW\PHPStan\GwValues;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\Native\NativeParameterReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\ClosureType;
use PHPStan\Type\VerbosityLevel;

final class ArrayValueCallbackRule implements Rule
{
    /** @var RuleLevelHelper */
    private $ruleLevelHelper;

    public function __construct(RuleLevelHelper $ruleLevelHelper)
    {
        $this->ruleLevelHelper = $ruleLevelHelper;
    }

    public function getNodeType(): string
    {
        return Node\Expr\MethodCall::class;
    }

    /**
     * @return string[] errors
     */
    public function processNode(Node $methodCall, Scope $scope): array
    {
        if (!$methodCall instanceof Node\Expr\MethodCall) {
            return [];
        }

        if (!\in_array($methodCall->name->name, ['map', 'filter', 'reduce'], true)) {
            return [];
        }

        $valueType = $scope->getType($methodCall->var);

        if (!$valueType instanceof ArrayValueType) {
            return [];
        }

        $firstAttribute = $methodCall->args[0]->value;
        $callableType = $scope->getType($firstAttribute);

        if ($callableType instanceof ClosureType) {
            /** @var NativeParameterReflection[] $parameters */
            $parameters = $callableType->getParameters();
            $parameter = $parameters[0];
            $parameterType = $parameters[0]->getType();
            $argumentValueType = $valueType->innerType();

            $accepts = $this->ruleLevelHelper->accepts(
                $parameterType,
                $argumentValueType,
                $scope->isDeclareStrictTypes()
            );

            if (!$accepts) {
                return [
                    \sprintf(
                        'Parameter #1 %s of method ' . $methodCall->name->name . ' callback expects %s, %s given.',
                        sprintf('%s$%s', $parameter->isVariadic() ? '...' : '', $parameter->getName()),
                        $parameterType->describe(VerbosityLevel::typeOnly()),
                        $argumentValueType->describe(
                            $parameterType->isCallable()->yes() ? VerbosityLevel::value() : VerbosityLevel::typeOnly()
                        )
                    ),
                ];
            }
        }

        return [];
    }
}
