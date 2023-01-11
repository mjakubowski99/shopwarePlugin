<?php

declare(strict_types=1);

namespace ProductExtension\Subscriber;

use ProductExtension\Core\ProductBadgeNames;
use ProductExtension\MessageQueue\Message\ProductBadgeNotification;
use Shopware\Core\Checkout\Cart\Event\BeforeLineItemAddedEvent;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

class BeforeLineItemAddedEventSubscriber implements EventSubscriberInterface
{
    private MessageBusInterface $bus;
    private EntityRepository $productBadgeRepository;

    public function __construct(MessageBusInterface $bus, EntityRepository $productBadgeRepository)
    {
        $this->bus = $bus;
        $this->productBadgeRepository = $productBadgeRepository;
    }

    public function onBeforeLineItemAdded(BeforeLineItemAddedEvent $event): void
    {
        if ($event->getLineItem()->getType() === LineItem::PRODUCT_LINE_ITEM_TYPE) {
            return;
        }
        if ($event->getLineItem()->getReferencedId() === null) {
            return;
        }

        $this->addBadgeToMessageQueue($event->getLineItem()->getReferencedId(), $event->getContext());
    }

    public function addBadgeToMessageQueue(string $productId, Context $context): void
    {
        try{
            $productBadge = $this->getBadgeForProduct($productId, $context);
        } catch (NotFoundHttpException $exception) {
            $productBadge = $this->createBadgeForProductWithRandomNameInDefaultLanguage($productId, $context);
        }

        $this->bus->dispatch(new ProductBadgeNotification($productBadge->get('name')));
    }

    private function getBadgeForProduct(string $productId, Context $context): ArrayEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productId', $productId));

        $productBadge = $this->productBadgeRepository->search($criteria, $context)->first();

        if ($productBadge === null) {
            throw new NotFoundHttpException("Badge for product not found");
        }


        /** @var ArrayEntity */
        return $productBadge;
    }

    private function createBadgeForProductWithRandomNameInDefaultLanguage(string $productId, Context $context): ArrayEntity
    {
        $this->productBadgeRepository->create([
            'productId' => $productId,
            'translations' => [
                Defaults::LANGUAGE_SYSTEM => [
                    'name' => array_rand(ProductBadgeNames::ALL)
                ]
            ]
        ], $context);

        return $this->getBadgeForProduct($productId, $context);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeLineItemAddedEvent::class => 'onBeforeLineItemAdded'
        ];
    }
}
