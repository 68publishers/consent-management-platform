<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220714014443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Added column "necessary" into category table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category ADD necessary BOOLEAN NOT NULL DEFAULT FALSE');
        $this->addSql('ALTER TABLE category ALTER COLUMN necessary DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category DROP necessary');
    }
}
