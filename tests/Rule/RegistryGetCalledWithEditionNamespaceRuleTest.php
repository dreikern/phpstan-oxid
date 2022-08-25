<?php declare(strict_types=1);

namespace dreikern\PhpStanOxid\Tests\Rule;

use dreikern\PhpStanOxid\Resolver\IsolatedClassChainResolver;
use dreikern\PhpStanOxid\Rule\RegistryGetCalledWithEditionNamespaceRule;
use PHPStan\Testing\RuleTestCase;

class RegistryGetCalledWithEditionNamespaceRuleTest extends RuleTestCase
{
    protected function getRule(): \PHPStan\Rules\Rule
    {
        return new RegistryGetCalledWithEditionNamespaceRule(
            new IsolatedClassChainResolver(getenv('PHPSTAN_OXID_CONFIG_PATH')),
            $this->createReflectionProvider()
        );
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__.'/data/registry-get-with-edition-namespace.php'], [
            [
                'Registry::get() call with edition namespace for class OxidEsales\EshopCommunity\Application\Controller\Admin\ArticleController. Use OxidEsales\Eshop\Application\Controller\Admin\ArticleController instead.',
                3,
            ],
        ]);
    }
}
