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

        if (!\in_array(
            $methodCall->name->name,
            ['map', 'filter', 'reduce', 'each', 'sort', 'unique', 'any', 'every'],
            true
        )) {
            return [];
        }

        $numberOfParameters = 1;

        if (\in_array($methodCall->name->name, ['unique', 'sort'], true)) {
            $numberOfParameters = 2;
        }

        $valueType = $scope->getType($methodCall->var);

        if (!$valueType instanceof ArrayValueType) {
            return [];
        }

        $errors = [];

        for ($parameterIndex = 0; $parameterIndex < $numberOfParameters; $parameterIndex++) {
            $attribute = $methodCall->args[0]->value;
            $callableType = $scope->getType($attribute);

            if ($callableType instanceof ClosureType) {
                /** @var NativeParameterReflection[] $parameters */
                $parameters = $callableType->getParameters();
                $parameter = $parameters[$parameterIndex];
                $parameterType = $parameters[$parameterIndex]->getType();
                $argumentValueType = $valueType->innerType();

                $accepts = $this->ruleLevelHelper->accepts(
                    $parameterType,
                    $argumentValueType,
                    $scope->isDeclareStrictTypes()
                );

                if (!$accepts) {
                    $errors[] = \sprintf(
                        'Parameter #%d %s of method ' . $methodCall->name->name . ' callback expects %s, %s given.',
                        $parameterIndex + 1,
                        sprintf('%s$%s', $parameter->isVariadic() ? '...' : '', $parameter->getName()),
                        $parameterType->describe(VerbosityLevel::typeOnly()),
                        $argumentValueType->describe(
                            $parameterType->isCallable()->yes() ? VerbosityLevel::value()
                                : VerbosityLevel::typeOnly()
                        )
                    );
                }
            }
        }

        return $errors;
    }
}
