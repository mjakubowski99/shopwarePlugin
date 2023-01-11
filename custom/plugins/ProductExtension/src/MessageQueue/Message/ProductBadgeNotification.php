<?php

declare(strict_types=1);

namespace ProductExtension\MessageQueue\Message;

class ProductBadgeNotification
{
    private string $badgeName;

    public function __construct(string $badgeName)
    {
        $this->badgeName = $badgeName;
    }

    public function getBadgeName(): string
    {
        return $this->badgeName;
    }
}
