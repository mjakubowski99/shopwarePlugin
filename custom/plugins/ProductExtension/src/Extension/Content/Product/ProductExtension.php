<?php

declare(strict_types=1);

namespace ProductExtension\Extension\Content\Product;

use ProductExtension\Core\Content\ProductBadge\ProductBadgeDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToOneAssociationField(
                'productBadge',
                'product_badge_id',
                'id',
                ProductBadgeDefinition::class,
                false
            ))
        );
    }

    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }
}

