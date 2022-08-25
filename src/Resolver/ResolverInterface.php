<?php declare(strict_types=1);

namespace dreikern\PhpStanOxid\Resolver;

interface ResolverInterface
{
    public function getLastActiveChildClass(string $className): ?string;

    public function getUnifiedClassNameForLegacyClass(string $className): ?string;

    public function getUnifiedClassName(string $fqdn): string;

    public function registerAliases(): void;
}
