<?php declare(strict_types=1);

namespace ProductExtension\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1673431966ProductBadge extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1673431966;
    }

    /** @throws \Doctrine\DBAL\Exception */
    public function update(Connection $connection): void
    {
        $query = <<<SQL
            CREATE TABLE IF NOT EXISTS `product_badge` (
                `id` BINARY(16) NOT NULL,
                `product_id` BINARY(16) NOT NULL UNIQUE,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `fk.product_badge.product_id` FOREIGN KEY (`product_id`)
                REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            )
            ENGINE = InnoDB
            DEFAULT CHARSET = utf8mb4
            COLLATE = utf8mb4_unicode_ci;
        SQL;

        $connection->executeStatement($query);
    }

    /** @throws \Doctrine\DBAL\Exception */
    public function updateDestructive(Connection $connection): void
    {
        $query = <<<SQL
            ALTER TABLE product_badge DROP CONSTRAINT `fk.product_badge.product_id`;
            ALTER TABLE product_badge_translation DROP CONSTRAINT `fk.product_badge_translation.product_badge_id`;
            DROP TABLE product_badge
        SQL;
        $connection->executeStatement($query);

        $this->update($connection);
    }
}
