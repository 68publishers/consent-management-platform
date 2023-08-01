<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220525022437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds table for global settings.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE global_settings (id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, last_update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, locales JSON NOT NULL, version INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN global_settings.id IS \'(DC2Type:App\\Domain\\GlobalSettings\\ValueObject\\GlobalSettingsId)\'');
        $this->addSql('COMMENT ON COLUMN global_settings.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN global_settings.last_update_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN global_settings.locales IS \'(DC2Type:App\\Domain\\Shared\\ValueObject\\Locales)\'');
        $this->addSql('CREATE TABLE global_settings_event_stream (id BIGSERIAL NOT NULL, event_id UUID NOT NULL, aggregate_id UUID NOT NULL, event_name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, parameters JSON NOT NULL, metadata JSONB NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_global_settings_event_stream_event_id ON global_settings_event_stream (event_id)');
        $this->addSql('CREATE INDEX idx_global_settings_event_stream_aggregate_id ON global_settings_event_stream (aggregate_id)');
        $this->addSql('CREATE INDEX idx_global_settings_event_stream_created_at ON global_settings_event_stream (created_at)');
        $this->addSql('CREATE UNIQUE INDEX uniq_global_settings_event_stream_event_id ON global_settings_event_stream (event_id)');
        $this->addSql('COMMENT ON COLUMN global_settings_event_stream.event_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\EventId)\'');
        $this->addSql('COMMENT ON COLUMN global_settings_event_stream.aggregate_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\AggregateId)\'');
        $this->addSql('COMMENT ON COLUMN global_settings_event_stream.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE global_settings');
        $this->addSql('DROP TABLE global_settings_event_stream');
    }
}
