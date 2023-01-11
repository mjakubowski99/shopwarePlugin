<?php

declare(strict_types=1);

namespace ProductExtension\Core\Content\ProductBadge;

use ProductExtension\Core\Content\ProductBadge\Aggregate\Translation\ProductBadgeTranslationDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductBadgeDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'product_badge';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new ApiAware(), new Required()),
            (new FkField('product_id', 'productId', ProductDefinition::class))->addFlags(new ApiAware()),
            (new OneToOneAssociationField('product', 'product_id', 'id', ProductDefinition::class, false))
                ->addFlags(new ApiAware()),
            (new TranslatedField('name'))->addFlags(new ApiAware(), new Required()),
            (new TranslationsAssociationField(
                ProductBadgeTranslationDefinition::class,
                'product_badge_id'
            ))->addFlags(new ApiAware(), new Required())
        ]);
    }
}
