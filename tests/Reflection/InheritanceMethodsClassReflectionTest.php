<?php declare(strict_types=1);

namespace dreikern\PhpStanOxid\Tests\Reflection;

use dreikern\PhpStanOxid\Reflection\InheritanceMethodsClassReflection;
use dreikern\PhpStanOxid\Resolver\IsolatedClassChainResolver;
use OxidEsales\Eshop\Application\Model\Voucher;
use OxidEsales\Eshop\Core\Decryptor;
use OxidEsales\Eshop\Core\ViewConfig;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\ShouldNotHappenException;
use PHPStan\Testing\PHPStanTestCase;

class InheritanceMethodsClassReflectionTest extends PHPStanTestCase
{
    private ReflectionProvider $reflectionProvider;
    private InheritanceMethodsClassReflection $extension;

    protected function setUp(): void
    {
        $this->reflectionProvider = $this->createReflectionProvider();
        $this->extension = new InheritanceMethodsClassReflection(
            new IsolatedClassChainResolver(getenv('PHPSTAN_OXID_CONFIG_PATH')),
            $this->reflectionProvider
        );
    }

    /**
     * @return mixed[]
     */
    public function dataHasMethod(): array
    {
        return [
            [ViewConfig::class, 'test_newMethod', true],
            [ViewConfig::class, 'test_arbitraryMethod', false],
            [Voucher::class, 'test_arbitraryMethod', false],
        ];
    }

    /**
     * @dataProvider dataHasMethod
     */
    public function testHasMethod(string $className, string $method, bool $expectedResult): void
    {
        $classReflection = $this->reflectionProvider->getClass($className);

        self::assertSame($expectedResult, $this->extension->hasMethod($classReflection, $method));
    }

    public function testGetMethod(): void
    {
        $classReflection = $this->reflectionProvider->getClass(ViewConfig::class);
        $methodReflection = $this->extension->getMethod($classReflection, 'test_newMethod');

        self::assertSame('test_newMethod', $methodReflection->getName());
    }

    public function testGetMethodWithClassNotExtended(): void
    {
        $classReflection = $this->reflectionProvider->getClass(Decryptor::class);

        $this->expectException(ShouldNotHappenException::class);

        $this->extension->getMethod($classReflection, 'test_newMethod');
    }
}
