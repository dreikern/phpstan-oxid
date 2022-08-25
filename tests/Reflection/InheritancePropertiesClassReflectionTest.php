<?php declare(strict_types=1);

namespace dreikern\PhpStanOxid\Tests\Reflection;

use dreikern\PhpStanOxid\Reflection\InheritancePropertiesClassReflection;
use dreikern\PhpStanOxid\Resolver\IsolatedClassChainResolver;
use OxidEsales\Eshop\Application\Model\Voucher;
use OxidEsales\Eshop\Core\Decryptor;
use OxidEsales\Eshop\Core\ViewConfig;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\PHPStanTestCase;

class InheritancePropertiesClassReflectionTest extends PHPStanTestCase
{
    private ReflectionProvider $reflectionProvider;
    private InheritancePropertiesClassReflection $extension;

    protected function setUp(): void
    {
        $this->reflectionProvider = $this->createReflectionProvider();
        $this->extension = new InheritancePropertiesClassReflection(
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
            [ViewConfig::class, 'test_newProperty', true],
            [ViewConfig::class, 'test_arbitraryProperty', false],
            [Voucher::class, 'test_arbitraryProperty', false],
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
        $propertyReflection = $this->extension->getProperty($classReflection, 'test_newProperty');

        self::assertSame('test_newProperty', $propertyReflection->getNativeReflection()->getName());
    }

    public function testGetProperyWithClassNotExtended(): void
    {
        $classReflection = $this->reflectionProvider->getClass(Decryptor::class);

        $this->expectException(ShouldNotHappenException::class);

        $this->extension->getProperty($classReflection, 'test_newProperty');
    }
}
