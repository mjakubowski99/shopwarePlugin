<?php

declare(strict_types=1);

namespace ProductExtension\Core\Content\ProductBadge;

use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ProductBadge
{
    use EntityIdTrait;

    protected string $name;

    protected string $productId;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setProductId(string $productId): void
    {
        $this->productId = $productId;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }
}
