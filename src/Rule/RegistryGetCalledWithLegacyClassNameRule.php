<?php declare(strict_types=1);

namespace dreikern\PhpStanOxid\Rule;

use dreikern\PhpStanOxid\Resolver\ResolverInterface;
use OxidEsales\EshopCommunity\Core\Registry;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantStringType;

/**
 * @implements \PHPStan\Rules\Rule<Node\Expr\StaticCall>
 */
class RegistryGetCalledWithLegacyClassNameRule implements Rule
{
    private ResolverInterface $resolver;
    private ReflectionProvider $reflectionProvider;

    public function __construct(ResolverInterface $resolver, ReflectionProvider $reflectionProvider)
    {
        $this->resolver = $resolver;
        $this->reflectionProvider = $reflectionProvider;
    }

    public function getNodeType(): string
    {
        return Node\Expr\StaticCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->name instanceof Node\Identifier || !$node->class instanceof Node\Name\FullyQualified) {
            return [];
        }

        $funcCallName = $node->name->toString();
        $className = $node->class->toString();

        if (!class_exists($className) || !$this->reflectionProvider->getClass($className)->isSubclassOf(Registry::class)
            || 'get' !== strtolower($funcCallName)) {
            return [];
        }

        $firstArg = $node->getArgs()[0];
        $formatArgType = $scope->getType($firstArg->value);

        if (count($formatArgType->getConstantStrings()) === 0) {
            return [];
        }

        foreach ($formatArgType->getConstantStrings() as $constantString) {
            $newFqdn = $this->resolver->getUnifiedClassNameForLegacyClass($constantString->getValue());

            if (!$newFqdn) {
                return [];
            }

            return [
                RuleErrorBuilder::message(
                    sprintf('Registry::get() call with legacy className %s. Use %s instead.', $constantString->getValue(), $newFqdn)
                )
                    ->identifier('oxid.rule.RegistryGetCalledWithLegacyClassNameRule')
                    ->build(),
            ];
        }
    }
}
