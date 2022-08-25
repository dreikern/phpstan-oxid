<?php declare(strict_types=1);

namespace dreikern\PhpStanOxid\Reflection;

use dreikern\PhpStanOxid\Resolver\ResolverInterface;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\ShouldNotHappenException;

class InheritanceMethodsClassReflection implements MethodsClassReflectionExtension
{
    private ResolverInterface $resolver;
    private ReflectionProvider $reflectionProvider;

    public function __construct(ResolverInterface $resolver, ReflectionProvider $reflectionProvider)
    {
        $this->resolver = $resolver;
        $this->reflectionProvider = $reflectionProvider;
    }

    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        $childClassName = $this->resolver->getLastActiveChildClass($classReflection->getName());

        if (!$childClassName) {
            return false;
        }

        $childClassReflection = $this->reflectionProvider->getClass($childClassName);

        return $childClassReflection->hasNativeMethod($methodName);
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        $childClassName = $this->resolver->getLastActiveChildClass($classReflection->getName());

        if (!$childClassName) {
            throw new ShouldNotHappenException();
        }

        $childClassReflection = $this->reflectionProvider->getClass($childClassName);

        return $childClassReflection->getNativeMethod($methodName);
    }
}
