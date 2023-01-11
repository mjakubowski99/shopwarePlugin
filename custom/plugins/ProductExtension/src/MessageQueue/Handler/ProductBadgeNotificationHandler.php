<?php

declare(strict_types=1);

namespace ProductExtension\MessageQueue\Handler;

use GuzzleHttp\Client;
use ProductExtension\MessageQueue\Message\ProductBadgeNotification;
use Shopware\Core\Framework\MessageQueue\Handler\AbstractMessageHandler;

class ProductBadgeNotificationHandler extends AbstractMessageHandler
{
    public static function getHandledMessages(): iterable
    {
        return [ProductBadgeNotification::class];
    }

    /** @var ProductBadgeNotification $message */
    public function handle($message): void
    {
        $client = new Client();
        $client->get('/'.$message->getBadgeName());
    }
}
