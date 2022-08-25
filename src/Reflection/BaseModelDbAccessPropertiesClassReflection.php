<?php declare(strict_types=1);

namespace dreikern\PhpStanOxid\Reflection;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\BaseModel;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\ObjectType;

class BaseModelDbAccessPropertiesClassReflection implements PropertiesClassReflectionExtension
{
    public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
    {
        if (BaseModel::class !== $classReflection->getName() && !$classReflection->isSubclassOf(BaseModel::class)) {
            return false;
        }

        return (bool) preg_match('/.*__.*/', $propertyName);
    }

    public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
    {
        $type = new ObjectType(Field::class);

        return new ObjectPropertyReflection($classReflection, $type);
    }
}
