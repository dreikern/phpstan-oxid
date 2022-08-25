<?php declare(strict_types=1);

namespace dreikern\PhpStanOxid\Rule;

use dreikern\PhpStanOxid\Resolver\ResolverInterface;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantStringType;

/**
 * @implements \PHPStan\Rules\Rule<Node\Expr\FuncCall>
 */
class OxNewCalledWithLegacyClassNameRule implements Rule
{
    private ResolverInterface $resolver;

    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    public function getNodeType(): string
    {
        return Node\Expr\FuncCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->name instanceof Node\Name) {
            return [];
        }

        $funcCallName = $node->name->toString();

        if ('oxnew' !== strtolower($funcCallName)) {
            return [];
        }

        $firstArg = $node->getArgs()[0];
        $formatArgType = $scope->getType($firstArg->value);

        if (!$formatArgType instanceof ConstantStringType) {
            return [];
        }

        $newFqdn = $this->resolver->getUnifiedClassNameForLegacyClass($formatArgType->getValue());

        if (!$newFqdn) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                sprintf('oxNew() call with legacy className %s. Use %s instead.', $formatArgType->getValue(), $newFqdn)
            )->build(),
        ];
    }
}
