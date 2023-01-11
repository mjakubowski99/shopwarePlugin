<?php

declare(strict_types=1);

namespace ProductExtension\Subscriber;

use Psr\Log\LoggerInterface;
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
    private LoggerInterface $logger;
    private EntityRepository $entityRepository;

    public function __construct(MessageBusInterface $bus, LoggerInterface $logger, EntityRepository $entityRepository)
    {
        $this->bus = $bus;
        $this->logger = $logger;
        $this->entityRepository = $entityRepository;
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
        $criteria->addFilter(new EqualsFilter('id', $event->getLineItem()->getReferencedId()));

        $product = $this->entityRepository->search($criteria, $event->getContext())->first();

        $this->logger->alert($product->productBadge);
    }
}
