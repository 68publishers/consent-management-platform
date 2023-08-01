<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220526201704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Added column "default_locale" for tables "global_settings" and "project".';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE global_settings ADD default_locale VARCHAR(255) DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN global_settings.default_locale IS \'(DC2Type:App\\Domain\\Shared\\ValueObject\\Locale)\'');
        $this->addSql('ALTER TABLE project ADD default_locale VARCHAR(255) DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN project.default_locale IS \'(DC2Type:App\\Domain\\Shared\\ValueObject\\Locale)\'');

        $this->addSql('UPDATE global_settings SET default_locale = sub.default_locale FROM (SELECT gs.id, gs.locales->>0 AS default_locale FROM global_settings gs) sub WHERE global_settings.id = sub.id');
        $this->addSql('UPDATE project SET default_locale = sub.default_locale FROM (SELECT p.id, p.locales->>0 AS default_locale FROM project p) sub WHERE project.id = sub.id');

        $this->addSql('ALTER TABLE global_settings ALTER COLUMN default_locale SET NOT NULL');
        $this->addSql('ALTER TABLE project ALTER COLUMN default_locale SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE global_settings DROP default_locale');
        $this->addSql('ALTER TABLE project DROP default_locale');
    }
}
