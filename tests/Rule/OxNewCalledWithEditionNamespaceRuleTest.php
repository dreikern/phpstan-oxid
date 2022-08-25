<?php declare(strict_types=1);

namespace dreikern\PhpStanOxid\Tests\Rule;

use dreikern\PhpStanOxid\Resolver\IsolatedClassChainResolver;
use dreikern\PhpStanOxid\Rule\OxNewCalledWithEditionNamespaceRule;
use PHPStan\Testing\RuleTestCase;

class OxNewCalledWithEditionNamespaceRuleTest extends RuleTestCase
{
    protected function getRule(): \PHPStan\Rules\Rule
    {
        return new OxNewCalledWithEditionNamespaceRule(new IsolatedClassChainResolver(getenv('PHPSTAN_OXID_CONFIG_PATH')));
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__.'/data/oxnew-with-edition-namespace.php'], [
            [
                'oxNew() call with edition namespace for class OxidEsales\EshopCommunity\Application\Controller\Admin\ArticleController. Use OxidEsales\Eshop\Application\Controller\Admin\ArticleController instead.',
                3,
            ],
        ]);
    }
}
