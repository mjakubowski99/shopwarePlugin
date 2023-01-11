<?php

declare(strict_types=1);

namespace ProductExtension\Core\Content\ProductBadge\Aggregate\Translation;

use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;
use ProductExtension\Core\Content\ProductBadge\ProductBadge;

class ProductBadgeTranslation extends TranslationEntity
{
    private string $productBadgeId;

    private string $name;

    private ProductBadge $productBadge;

    public function getProductBadgeId(): string
    {
        return $this->productBadgeId;
    }

    public function setProductBadgeId(string $productBadgeId): void
    {
        $this->productBadgeId = $productBadgeId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getProductBadge(): ProductBadge
    {
        return $this->productBadge;
    }

    public function setProductBadge(ProductBadge $productBadge): void
    {
        $this->productBadge = $productBadge;
    }
}
