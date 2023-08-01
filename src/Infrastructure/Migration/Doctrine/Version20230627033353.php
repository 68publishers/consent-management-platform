<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230627033353 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds column "cookie_suggestion"."ignored_permanently".';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cookie_suggestion ADD ignored_permanently BOOLEAN NOT NULL DEFAULT FALSE');
        $this->addSql('ALTER TABLE cookie_suggestion ALTER COLUMN ignored_permanently DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cookie_suggestion DROP ignored_permanently');
    }
}
