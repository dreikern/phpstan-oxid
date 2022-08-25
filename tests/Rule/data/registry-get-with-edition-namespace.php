<?php declare(strict_types=1);

\OxidEsales\Eshop\Core\Registry::get(\OxidEsales\EshopCommunity\Application\Controller\Admin\ArticleController::class);
\OxidEsales\Eshop\Core\Registry::get(true);
\OxidEsales\Eshop\Core\Registry::get('NO_CLASS');

(new \DateTime())::getLastErrors();

\OxidEsales\EshopCommunity\Internal\Container\ContainerFactory::get(\OxidEsales\EshopCommunity\Application\Controller\Admin\ArticleController::class);
