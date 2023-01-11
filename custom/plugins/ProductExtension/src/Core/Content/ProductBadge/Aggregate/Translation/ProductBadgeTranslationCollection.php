<?php

declare(strict_types=1);

namespace ProductExtension\Core\Content\ProductBadge\Aggregate\Translation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class ProductBadgeTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ProductBadgeTranslation::class;
    }
}
