<?php declare(strict_types=1);

namespace dreikern\PhpStanOxid\Tests\Reflection;

use dreikern\PhpStanOxid\Reflection\BaseModelDbAccessPropertiesClassReflection;
use dreikern\PhpStanOxid\Resolver\IsolatedClassChainResolver;
use OxidEsales\Eshop\Application\Model\Voucher;
use OxidEsales\Eshop\Core\ViewConfig;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Testing\PHPStanTestCase;
use PHPStan\Type\ObjectType;

class BaseModelDbAccessPropertiesClassReflectionTest extends PHPStanTestCase
{
    private ReflectionProvider $reflectionProvider;
    private BaseModelDbAccessPropertiesClassReflection $extension;

    protected function setUp(): void
    {
        $this->reflectionProvider = $this->createReflectionProvider();
        $this->extension = new BaseModelDbAccessPropertiesClassReflection(
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
            [Voucher::class, 'oxvoucher__oxid', true],
            [ViewConfig::class, 'oxvoucher__oxid', false],
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
        $classReflection = $this->reflectionProvider->getClass(ViewConfig::class);
        $propertyReflection = $this->extension->getProperty($classReflection, 'oxvoucher__oxid');

        self::assertInstanceOf(ObjectType::class, $propertyReflection->getReadableType());
    }
}
