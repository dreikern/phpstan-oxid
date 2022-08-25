<?php declare(strict_types=1);

namespace dreikern\PhpStanOxid\Tests\Rule;

use dreikern\PhpStanOxid\Resolver\IsolatedClassChainResolver;
use dreikern\PhpStanOxid\Rule\RegistryGetCalledWithLegacyClassNameRule;
use PHPStan\Testing\RuleTestCase;

class RegistryGetCalledWithLegacyClassNameRuleTest extends RuleTestCase
{
    protected function getRule(): \PHPStan\Rules\Rule
    {
        return new RegistryGetCalledWithLegacyClassNameRule(
            new IsolatedClassChainResolver(getenv('PHPSTAN_OXID_CONFIG_PATH')),
            $this->createReflectionProvider()
        );
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__.'/data/registry-get-with-legacy-classname.php'], [
            [
                'Registry::get() call with legacy className oxarticle. Use OxidEsales\Eshop\Application\Model\Article instead.',
                3,
            ],
        ]);
    }
}
