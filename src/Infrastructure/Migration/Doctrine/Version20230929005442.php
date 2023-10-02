<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230929005442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds column "global_settings"."environments".';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE global_settings ADD environments JSONB NOT NULL DEFAULT \'[]\'');
        $this->addSql('ALTER TABLE global_settings ALTER COLUMN environments DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN global_settings.environments IS \'(DC2Type:App\\Domain\\GlobalSettings\\ValueObject\\Environments)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE global_settings DROP environments');
    }
}
