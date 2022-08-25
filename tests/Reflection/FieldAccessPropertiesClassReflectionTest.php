<?php declare(strict_types=1);

namespace dreikern\PhpStanOxid\Tests\Reflection;

use dreikern\PhpStanOxid\Reflection\FieldAccessPropertiesClassReflection;
use dreikern\PhpStanOxid\Reflection\ObjectPropertyReflection;
use dreikern\PhpStanOxid\Resolver\IsolatedClassChainResolver;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\ViewConfig;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Testing\PHPStanTestCase;
use PHPStan\TrinaryLogic;
use PHPStan\Type\StringType;

class FieldAccessPropertiesClassReflectionTest extends PHPStanTestCase
{
    private ReflectionProvider $reflectionProvider;
    private FieldAccessPropertiesClassReflection $extension;

    protected function setUp(): void
    {
        $this->reflectionProvider = $this->createReflectionProvider();
        $this->extension = new FieldAccessPropertiesClassReflection(
            new IsolatedClassChainResolver(getenv('PHPSTAN_OXID_CONFIG_PATH')),
            $this->reflectionProvider
        );
    }

    /**
     * @return mixed[]
     */
    public function dataHasProperty(): array
    {
        return [
            [Field::class, 'value', true],
            [Field::class, 'rawValue', true],
            [ViewConfig::class, 'value', false],
            [ViewConfig::class, 'rawValue', false],
        ];
    }

    /**
     * @dataProvider dataHasProperty
     */
    public function testHasProperty(string $className, string $property, bool $expectedResult): void
    {
        $classReflection = $this->reflectionProvider->getClass($className);

        self::assertSame($expectedResult, $this->extension->hasProperty($classReflection, $property));
    }

    public function testGetProperty(): void
    {
        $classReflection = $this->reflectionProvider->getClass(Field::class);
        $propertyReflection = $this->extension->getProperty($classReflection, 'value');

        self::assertInstanceOf(ObjectPropertyReflection::class, $propertyReflection);
        self::assertInstanceOf(get_class($classReflection), $propertyReflection->getDeclaringClass());
        self::assertInstanceOf(StringType::class, $propertyReflection->getReadableType());
        self::assertInstanceOf(StringType::class, $propertyReflection->getWritableType());
        self::assertInstanceOf(StringType::class, $propertyReflection->getWritableType());
        self::assertEquals(TrinaryLogic::createNo(), $propertyReflection->isDeprecated());
        self::assertEquals(TrinaryLogic::createNo(), $propertyReflection->isInternal());

        self::assertFalse($propertyReflection->isStatic());
        self::assertFalse($propertyReflection->isPrivate());
        self::assertTrue($propertyReflection->isPublic());
        self::assertTrue($propertyReflection->isReadable());
        self::assertTrue($propertyReflection->isWritable());
        self::assertTrue($propertyReflection->canChangeTypeAfterAssignment());
        self::assertNull($propertyReflection->getDeprecatedDescription());
        self::assertNull($propertyReflection->getDocComment());
    }
}
