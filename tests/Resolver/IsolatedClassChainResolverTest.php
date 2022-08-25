<?php declare(strict_types=1);

namespace dreikern\PhpStanOxid\Tests\Reflection;

use Acme\OxidProject\EnabledModule\Core\ViewConfig;
use dreikern\PhpStanOxid\Resolver\IsolatedClassChainResolver;
use OxidEsales\Eshop\Core\Config;
use PHPStan\Testing\PHPStanTestCase;

class IsolatedClassChainResolverTest extends PHPStanTestCase
{
    private string $configPath;
    private IsolatedClassChainResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new IsolatedClassChainResolver(getenv('PHPSTAN_OXID_CONFIG_PATH'));
    }

    public function testGetUnifiedClassName(): void
    {
        self::assertEquals(
            \OxidEsales\Eshop\Application\Model\Discount::class,
            $this->resolver->getUnifiedClassName(\OxidEsales\EshopCommunity\Application\Model\Discount::class)
        );
    }

    public function testGetUnifiedClassNameForLegacyClass(): void
    {
        self::assertEquals(
            \OxidEsales\Eshop\Application\Model\Discount::class,
            $this->resolver->getUnifiedClassNameForLegacyClass('oxdiscount')
        );
    }

    public function testGetLastActiveChildClass(): void
    {
        self::assertNull($this->resolver->getLastActiveChildClass(Config::class));
        self::assertEquals(ViewConfig::class, $this->resolver->getLastActiveChildClass(\OxidEsales\Eshop\Core\ViewConfig::class));
    }

    public function testGetLastActiveChildClassWithLegacyModule(): void
    {
        $env = getenv('PHPSTAN_OXID_CONFIG_PATH');
        putenv('PHPSTAN_OXID_CONFIG_PATH=');
        $this->resolver = new IsolatedClassChainResolver(__DIR__.'/data/module-config.yaml');

        self::assertEquals('user_counter', $this->resolver->getLastActiveChildClass(\OxidEsales\Eshop\Core\UserCounter::class));

        putenv(sprintf('PHPSTAN_OXID_CONFIG_PATH=%s', $env));
    }

    public function testRegisterAliases(): void
    {
        $env = getenv('PHPSTAN_OXID_CONFIG_PATH');
        putenv('PHPSTAN_OXID_CONFIG_PATH=');
        $this->resolver = new IsolatedClassChainResolver(__DIR__.'/data/module-config.yaml');

        self::assertFalse(class_exists('Acme\OxidProject\TestModule\Core\InputValidator_parent'));

        $this->resolver->registerAliases();

        self::assertTrue(class_exists('Acme\OxidProject\TestModule\Core\InputValidator_parent'));

        putenv(sprintf('PHPSTAN_OXID_CONFIG_PATH=%s', $env));
    }

    public function testReadShopConfigurationWithInvalidConfig(): void
    {
        $env = getenv('PHPSTAN_OXID_CONFIG_PATH');
        putenv('PHPSTAN_OXID_CONFIG_PATH=');

        $path = __DIR__.'/data/invalid-config.yaml';

        try {
            $this->resolver = new IsolatedClassChainResolver($path);
        } catch (\RuntimeException $e) {
            self::assertInstanceOf(\RuntimeException::class, $e);
            self::assertEquals(sprintf('Invalid yaml found in %s', $path), $e->getMessage());
        }

        putenv(sprintf('PHPSTAN_OXID_CONFIG_PATH=%s', $env));
    }

    public function testReadShopConfigurationWithInvalidConfigPath(): void
    {
        $env = getenv('PHPSTAN_OXID_CONFIG_PATH');
        putenv('PHPSTAN_OXID_CONFIG_PATH=');

        $path = __DIR__.'/data/not-existing-config.yaml';

        try {
            $this->resolver = new IsolatedClassChainResolver($path);
        } catch (\RuntimeException $e) {
            self::assertInstanceOf(\RuntimeException::class, $e);
            self::assertEquals(sprintf('Unable to find shopConfigurationPath %s', $path), $e->getMessage());
        }

        putenv(sprintf('PHPSTAN_OXID_CONFIG_PATH=%s', $env));
    }
}
