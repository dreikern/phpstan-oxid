<?php declare(strict_types=1);

namespace dreikern\PhpStanOxid\Type;

use dreikern\PhpStanOxid\Resolver\ResolverInterface;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

class OxNewFunctionDynamicReturnTypeExtension implements DynamicFunctionReturnTypeExtension
{
    private ResolverInterface $resolver;

    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    public function isFunctionSupported(
        FunctionReflection $functionReflection,
    ): bool {
        return 'oxnew' === strtolower($functionReflection->getName());
    }

    public function getTypeFromFunctionCall(
        FunctionReflection $functionReflection,
        FuncCall $functionCall,
        Scope $scope,
    ): ?Type {
        if (0 === \count($functionCall->getArgs())) {
            return ParametersAcceptorSelector::selectFromArgs(
                $scope,
                $functionCall->getArgs(),
                $functionReflection->getVariants()
            )->getReturnType();
        }

        $argType = $scope->getType($functionCall->getArgs()[0]->value);

        if (0 === count($argType->getConstantStrings())) {
            return null;
        }

        foreach ($argType->getConstantStrings() as $constantString) {
            $objectName = $constantString->getValue();
            $lastChildClass = $this->resolver->getLastActiveChildClass($objectName);

            if ($lastChildClass) {
                $classType = new ObjectType($lastChildClass);

                return $classType;
            }
        }

        return null;
    }
}
