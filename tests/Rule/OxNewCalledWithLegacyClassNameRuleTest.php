<?php declare(strict_types=1);

namespace dreikern\PhpStanOxid\Tests\Rule;

use dreikern\PhpStanOxid\Resolver\IsolatedClassChainResolver;
use dreikern\PhpStanOxid\Rule\OxNewCalledWithLegacyClassNameRule;
use PHPStan\Testing\RuleTestCase;

class OxNewCalledWithLegacyClassNameRuleTest extends RuleTestCase
{
    protected function getRule(): \PHPStan\Rules\Rule
    {
        return new OxNewCalledWithLegacyClassNameRule(new IsolatedClassChainResolver(getenv('PHPSTAN_OXID_CONFIG_PATH')));
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__.'/data/oxnew-with-legacy-classname.php'], [
            [
                'oxNew() call with legacy className oxarticle. Use OxidEsales\Eshop\Application\Model\Article instead.',
                3,
            ],
        ]);
    }
}
