<?php

declare(strict_types=1);

namespace ProductExtension\Core\Content\ProductBadge\Aggregate\Translation;

use ProductExtension\Core\Content\ProductBadge\ProductBadge;
use ProductExtension\Core\Content\ProductBadge\ProductBadgeDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductBadgeTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'product_badge_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getParentDefinitionClass(): string
    {
        return ProductBadgeDefinition::class;
    }

    public function getEntityClass(): string
    {
        return ProductBadgeTranslation::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new Required())
        ]);
    }
}
