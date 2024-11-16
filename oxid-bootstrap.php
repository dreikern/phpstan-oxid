<?php declare(strict_types=1);

/** @var PHPStan\DependencyInjection\Container $container */
/** @var dreikern\PhpStanOxid\Resolver\IsolatedClassChainResolver $resolver */
$resolver = $container->getByType(dreikern\PhpStanOxid\Resolver\ResolverInterface::class);
$resolver->registerAliases();
