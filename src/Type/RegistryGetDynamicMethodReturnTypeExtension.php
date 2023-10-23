<?php declare(strict_types=1);

namespace dreikern\PhpStanOxid\Type;

use dreikern\PhpStanOxid\Resolver\ResolverInterface;
use OxidEsales\Eshop\Core\Registry;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\ErrorType;
use PHPStan\Type\NeverType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

class RegistryGetDynamicMethodReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension
{
    private ResolverInterface $resolver;

    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    public function getClass(): string
    {
        return Registry::class;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return 'get' === strtolower($methodReflection->getName());
    }

    public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): ?Type
    {
        if (0 === \count($methodCall->getArgs())) {
            return new ErrorType();
        }

        $expr = $methodCall->getArgs()[0]->value;
        if ($expr instanceof String_) {
            $objectName = $expr->value;
            $lastChildClass = $this->resolver->getLastActiveChildClass($objectName);

            if ($lastChildClass) {
                return new ObjectType($lastChildClass);
            }

            return new ObjectType($expr->value);
        } elseif ($expr instanceof ClassConstFetch && $expr->class instanceof FullyQualified) {
            $objectName = $expr->class->toString();
            $lastChildClass = $this->resolver->getLastActiveChildClass($objectName);

            if ($lastChildClass) {
                return new ObjectType($lastChildClass);
            }

            return new ObjectType($objectName);
        }

        return new NeverType();
    }
}
