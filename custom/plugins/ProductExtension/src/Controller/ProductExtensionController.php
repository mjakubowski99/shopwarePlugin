<?php

declare(strict_types=1);

namespace ProductExtension\Controller;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;

/** @RouteScope (scopes={"storefront"}) */
class ProductExtensionController extends StorefrontController
{
    private SystemConfigService $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    /** @Route ("/productExtension", name="productExtension", methods={"GET"}) */
    public function showConfig(): Response
    {
        return $this->renderStorefront('@ProductExtension/productExtension/index.html.twig', [
            'config' => $this->systemConfigService->get('ProductExtension.config.email')
        ]);
    }
}

