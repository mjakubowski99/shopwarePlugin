<?php

declare(strict_types=1);

namespace ProductExtension\Subscriber;

use ProductExtension\Core\Content\ProductBadge\ProductBadge;
use ProductExtension\MessageQueue\Message\ProductAddedToCartNotification;
use Ramsey\Uuid\Uuid;
use Shopware\Core\Checkout\Cart\Event\BeforeLineItemAddedEvent;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class BeforeLineItemAddedEventSubscriber implements EventSubscriberInterface
{
    private MessageBusInterface $bus;
    private EntityRepository $productBadgeRepository;

    public function __construct(MessageBusInterface $bus, EntityRepository $productRepository)
    {
        $this->bus = $bus;
        $this->productBadgeRepository = $productRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeLineItemAddedEvent::class => 'onBeforeLineItemAdded'
        ];
    }

    public function onBeforeLineItemAdded(BeforeLineItemAddedEvent $event): void
    {
        if ($event->getLineItem()->getType() !== LineItem::PRODUCT_LINE_ITEM_TYPE) {
            return;
        }
        if ($event->getLineItem()->getReferencedId() === null) {
            return;
        }

        $criteria = new Criteria();
        $criteria->getAssociation('product')->addFilter(
            new EqualsFilter('id', $event->getLineItem()->getReferencedId())
        );

        /** @var ProductBadge $productBadge */
        $productBadge = $this->productBadgeRepository->search($criteria, $event->getContext())->first();
        if ($productBadge === null) {
            $this->productBadgeRepository->create([
                [
                    'name' => Uuid::uuid4()."-badge",
                    'product' => $event->getLineItem()->getReferencedId()
                ]
            ], $event->getContext());

            $productBadge = $this->productBadgeRepository->search($criteria, $event->getContext())->first();
        }

        $this->bus->dispatch(new ProductAddedToCartNotification($productBadge->getName()));
    }
}
