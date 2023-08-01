<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230612023129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds column "global_settings"."crawler_settings"';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE global_settings ADD crawler_settings JSONB NOT NULL DEFAULT \'{}\'');
        $this->addSql('COMMENT ON COLUMN global_settings.crawler_settings IS \'(DC2Type:App\\Domain\\GlobalSettings\\ValueObject\\CrawlerSettings)\'');
        $this->addSql('ALTER TABLE global_settings ALTER COLUMN crawler_settings DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE global_settings DROP crawler_settings');
    }
}
