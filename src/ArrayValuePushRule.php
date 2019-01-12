<?php declare(strict_types=1);

namespace GW\PHPStan\GwValues;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\VerbosityLevel;

final class ArrayValuePushRule implements Rule
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

        $identifier = $methodCall->name;

        if (!$identifier instanceof Node\Identifier) {
            return [];
        }

        if (!\in_array($methodCall->name->name, ['push', 'unshift'], true)) {
            return [];
        }

        $valueType = TypeHelper::searchArrayValueType($scope->getType($methodCall->var));

        if (!$valueType instanceof ArrayValueType) {
            return [];
        }

        $attribute = $methodCall->args[0]->value;
        $parameterType = $scope->getType($attribute);

        $accepts = $this->ruleLevelHelper->accepts(
            $parameterType,
            $valueType->innerType(),
            $scope->isDeclareStrictTypes()
        );

        $errors = [];

        if (!$accepts) {
            $errors[] = \sprintf(
                'Parameter #%d of method ' . $methodCall->name->name . ' callback expects %s, %s given.',
                1,
                $valueType->innerType()->describe(VerbosityLevel::typeOnly()),
                $parameterType->describe(
                    $parameterType->isCallable()->yes() ? VerbosityLevel::value()
                        : VerbosityLevel::typeOnly()
                )
            );
        }

        return $errors;
    }
}
