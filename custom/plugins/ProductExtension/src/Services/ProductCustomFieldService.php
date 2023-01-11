<?php

declare(strict_types=1);

namespace ProductExtension\Services;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class ProductCustomFieldService
{
    public const FIELD_SET_NAME = 'custom_product_fields';
    public const FIELD_NAME = 'product_history';

    private EntityRepository $customFieldSetRepository;

    public function __construct(EntityRepository $customFieldSetRepository)
    {
        $this->customFieldSetRepository = $customFieldSetRepository;
    }

    public function findProductCustomFieldSetIds(Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', self::FIELD_SET_NAME));

        return $this->customFieldSetRepository->searchIds($criteria, $context)->getIds();
    }

    public function createProductCustomFieldSet(Context $context)
    {
        if (count($this->findProductCustomFieldSetIds($context)) > 0) {
            return;
        }

        $this->customFieldSetRepository->create([
            [
                'name' => self::FIELD_SET_NAME,
                'config' => [
                    'label' => [
                        'en-GB' => 'Product history'
                    ]
                ],
                'customFields' => [],
                'relations' => [
                    [
                        'entityName' => 'product'
                    ]
                ]
            ]
        ], $context);
    }

    public function activateProductCustomField(Context $context)
    {
        $ids = $this->findProductCustomFieldSetIds($context);

        $updateData = [];
        foreach ($ids as $id){
            $updateData[] = [
                'id' => $id,
                'customFields' => [
                    [
                        'name' => self::FIELD_NAME,
                        'type' => CustomFieldTypes::TEXT,
                        'config' => [
                            'label' => [
                                'en-GB' => 'History of product'
                            ]
                        ]
                    ]
                ]
            ];
        }

        $this->customFieldSetRepository->update($updateData, $context);
    }

    public function deactivateProductCustomField(Context $context)
    {
        $this->deleteProductCustomFieldSet($context);
        $this->createProductCustomFieldSet($context);
    }

    public function deleteProductCustomFieldSet(Context $context)
    {
        $data = array_map(function(string $id){
            return ['id' => $id];
        }, $this->findProductCustomFieldSetIds($context));

        $this->customFieldSetRepository->delete($data, $context);
    }
}
