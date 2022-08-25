<?php declare(strict_types=1);

namespace dreikern\PhpStanOxid\Resolver;

use dreikern\PhpStanOxid\Data\OxidLegacyClassMap;
use Symfony\Component\Yaml\Yaml;

class IsolatedClassChainResolver implements ResolverInterface
{
    private string $shopConfigurationPath;
    private array $configuration = [];
    private array $activeClassExtensions = [];
    private array $nonUnifiedNamespaces = [
        'OxidEsales\EshopCommunity',
        'OxidEsales\EshopProfessional',
        'OxidEsales\EshopEnterprise',
    ];

    public function __construct(string $shopConfigurationPath)
    {
        $this->shopConfigurationPath = $shopConfigurationPath;

        if ($envConfigPath = getenv('PHPSTAN_OXID_CONFIG_PATH')) {
            $this->shopConfigurationPath = $envConfigPath;
        }

        $this->readShopConfiguration();
    }

    public function readShopConfiguration(): void
    {
        if (file_exists($this->shopConfigurationPath)) {
            $configurationFile = $this->shopConfigurationPath;
        } else {
            throw new \RuntimeException(sprintf('Unable to find shopConfigurationPath %s', $this->shopConfigurationPath));
        }

        $this->configuration = Yaml::parseFile($configurationFile);

        if (!isset($this->configuration['modules']) || !isset($this->configuration['moduleChains']['classExtensions']) || !count($this->configuration['moduleChains']['classExtensions'])) {
            throw new \RuntimeException(sprintf('Invalid yaml found in %s', $configurationFile));
        }

        foreach ($this->configuration['modules'] as $module) {
            if (!$module['configured'] || !isset($module['classExtensions']) || !is_array($module['classExtensions'])) {
                continue;
            }

            $this->activeClassExtensions = array_merge($this->activeClassExtensions, array_values($module['classExtensions']));
        }

        foreach ($this->configuration['moduleChains']['classExtensions'] as $baseClass => $classExtensions) {
            foreach ($classExtensions as $extensionKey => $classExtension) {
                if (!in_array($classExtension, $this->activeClassExtensions)) {
                    unset($this->configuration['moduleChains']['classExtensions'][$baseClass][$extensionKey]);
                }
            }
        }
    }

    public function getLastActiveChildClass(string $className): ?string
    {
        $className = $this->getUnifiedClassNameForLegacyClass($className) ?? $className;
        $className = $this->getUnifiedClassName($className);

        if (!isset($this->configuration['moduleChains']['classExtensions'][$className]) ||
            !count($this->configuration['moduleChains']['classExtensions'][$className])) {
            return null;
        }

        return $this->getClassName(end($this->configuration['moduleChains']['classExtensions'][$className]));
    }

    public function registerAliases(): void
    {
        foreach ($this->configuration['moduleChains']['classExtensions'] as $baseClass => $classExtensions) {
            $lastExtension = $baseClass;
            $chainLength = count($classExtensions);

            if ($chainLength) {
                foreach ($classExtensions as $classExtension) {
                    $class = $this->getClassName($lastExtension);
                    $alias = sprintf('%s_parent', $this->getClassName($classExtension));

                    class_alias($class, $alias);

                    $lastExtension = $classExtension;
                }
            }
        }
    }

    private function getClassName(string $className): string
    {
        if (false !== strpos($className, '/')) {
            return basename($className);
        }

        return $className;
    }

    public function getUnifiedClassName(string $className): string
    {
        return str_replace($this->nonUnifiedNamespaces, 'OxidEsales\Eshop', $className);
    }

    public function getUnifiedClassNameForLegacyClass(string $className): ?string
    {
        $className = strtolower($className);

        return OxidLegacyClassMap::$map[$className] ?? null;
    }
}
