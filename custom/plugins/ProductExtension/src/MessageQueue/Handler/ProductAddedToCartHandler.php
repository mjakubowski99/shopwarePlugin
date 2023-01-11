<?php

declare(strict_types=1);

namespace ProductExtension\MessageQueue\Handler;

use ProductExtension\MessageQueue\Message\ProductAddedToCartNotification;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Framework\MessageQueue\Handler\AbstractMessageHandler;
use GuzzleHttp\Client;

class ProductAddedToCartHandler extends AbstractMessageHandler
{
    /**
     * @param ProductAddedToCartNotification $message
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle($message): void
    {
        $client = new Client();
        $client->get('/'.$message->getBadgeName());
    }

    public static function getHandledMessages(): iterable
    {
        return [ProductAddedToCartNotification::class];
    }
}
