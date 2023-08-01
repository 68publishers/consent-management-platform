<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220525024459 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds column "locales" into the table "project".';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project ADD locales JSON NOT NULL DEFAULT \'[]\'');
        $this->addSql('ALTER TABLE project ALTER COLUMN locales DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN project.locales IS \'(DC2Type:App\\Domain\\Shared\\ValueObject\\Locales)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project DROP locales');
    }
}
