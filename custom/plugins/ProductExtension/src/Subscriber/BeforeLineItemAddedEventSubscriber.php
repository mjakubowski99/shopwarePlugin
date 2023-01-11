<?php

declare(strict_types=1);

namespace ProductExtension\Subscriber;

use ProductExtension\Core\Content\ProductBadge\ProductBadge;
use ProductExtension\Core\ProductBadgeNames;
use ProductExtension\MessageQueue\Message\ProductBadgeNotification;
use Shopware\Core\Checkout\Cart\Event\BeforeLineItemAddedEvent;
use Shopware\Core\Checkout\Cart\Event\CartChangedEvent;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
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

        $productId = $event->getLineItem()->getReferencedId();

        try{
            $productBadge = $this->getBadgeForProduct($productId, $event->getContext());
        } catch (NotFoundHttpException $exception) {
            $productBadge = $this->createBadgeForProductWithRandomNameInDefaultLanguage($productId, $event->getContext());
        }

        $this->bus->dispatch(new ProductBadgeNotification($productBadge->getName()));
    }

    private function getBadgeForProduct(string $productId, Context $context): ProductBadge
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('product_id', $productId));

        $productBadge = $this->productBadgeRepository->search($criteria, $context)->first();

        if ($productBadge === null) {
            throw new NotFoundHttpException("Badge for product not found");
        }

        /** @var ProductBadge */
        return $productBadge;
    }

    private function createBadgeForProductWithRandomNameInDefaultLanguage(string $productId, Context $context): ProductBadge
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
