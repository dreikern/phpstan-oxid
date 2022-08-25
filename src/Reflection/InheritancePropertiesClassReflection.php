<?php declare(strict_types=1);

namespace dreikern\PhpStanOxid\Reflection;

use dreikern\PhpStanOxid\Resolver\ResolverInterface;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\ShouldNotHappenException;

class InheritancePropertiesClassReflection implements PropertiesClassReflectionExtension
{
    private ResolverInterface $resolver;
    private ReflectionProvider $reflectionProvider;

    public function __construct(ResolverInterface $resolver, ReflectionProvider $reflectionProvider)
    {
        $this->resolver = $resolver;
        $this->reflectionProvider = $reflectionProvider;
    }

    public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
    {
        $childClassName = $this->resolver->getLastActiveChildClass($classReflection->getName());

        if (!$childClassName) {
            return false;
        }

        $childClassReflection = $this->reflectionProvider->getClass($childClassName);

        return $childClassReflection->hasNativeProperty($propertyName);
    }

    public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
    {
        $childClassName = $this->resolver->getLastActiveChildClass($classReflection->getName());

        if (!$childClassName) {
            throw new ShouldNotHappenException();
        }

        $childClassReflection = $this->reflectionProvider->getClass($childClassName);

        return $childClassReflection->getNativeProperty($propertyName);
    }
}
