<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220728014348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds a table for imports.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE import (id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, imported INT NOT NULL, failed INT NOT NULL, output TEXT NOT NULL, version INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_import_created_at ON import (created_at)');
        $this->addSql('COMMENT ON COLUMN import.id IS \'(DC2Type:App\\Domain\\Import\\ValueObject\\ImportId)\'');
        $this->addSql('COMMENT ON COLUMN import.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN import.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN import.name IS \'(DC2Type:App\\Domain\\Import\\ValueObject\\Name)\'');
        $this->addSql('COMMENT ON COLUMN import.status IS \'(DC2Type:App\\Domain\\Import\\ValueObject\\Status)\'');
        $this->addSql('COMMENT ON COLUMN import.author IS \'(DC2Type:App\\Domain\\Import\\ValueObject\\Author)\'');
        $this->addSql('COMMENT ON COLUMN import.imported IS \'(DC2Type:App\\Domain\\Import\\ValueObject\\Total)\'');
        $this->addSql('COMMENT ON COLUMN import.failed IS \'(DC2Type:App\\Domain\\Import\\ValueObject\\Total)\'');
        $this->addSql('COMMENT ON COLUMN import.output IS \'(DC2Type:App\\Domain\\Import\\ValueObject\\Output)\'');
        $this->addSql('CREATE TABLE import_event_stream (id BIGSERIAL NOT NULL, event_id UUID NOT NULL, aggregate_id UUID NOT NULL, event_name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, parameters JSON NOT NULL, metadata JSONB NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_import_event_stream_event_id ON import_event_stream (event_id)');
        $this->addSql('CREATE INDEX idx_import_event_stream_aggregate_id ON import_event_stream (aggregate_id)');
        $this->addSql('CREATE INDEX idx_import_event_stream_created_at ON import_event_stream (created_at)');
        $this->addSql('CREATE UNIQUE INDEX uniq_import_event_stream_event_id ON import_event_stream (event_id)');
        $this->addSql('COMMENT ON COLUMN import_event_stream.event_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\EventId)\'');
        $this->addSql('COMMENT ON COLUMN import_event_stream.aggregate_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\AggregateId)\'');
        $this->addSql('COMMENT ON COLUMN import_event_stream.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE import');
        $this->addSql('DROP TABLE import_event_stream');
    }
}
