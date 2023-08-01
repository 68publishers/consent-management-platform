<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220526000808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Added tables for categories and their translations.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE category_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE category (id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, code VARCHAR(70) NOT NULL, active BOOLEAN NOT NULL, version INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_category_created_at ON category (created_at)');
        $this->addSql('CREATE UNIQUE INDEX uniq_category_code ON category (lower(code)) WHERE deleted_at IS NULL');
        $this->addSql('COMMENT ON COLUMN category.id IS \'(DC2Type:App\\Domain\\Category\\ValueObject\\CategoryId)\'');
        $this->addSql('COMMENT ON COLUMN category.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN category.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN category.code IS \'(DC2Type:App\\Domain\\Category\\ValueObject\\Code)\'');
        $this->addSql('CREATE TABLE category_event_stream (id BIGSERIAL NOT NULL, event_id UUID NOT NULL, aggregate_id UUID NOT NULL, event_name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, parameters JSON NOT NULL, metadata JSONB NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_category_event_stream_event_id ON category_event_stream (event_id)');
        $this->addSql('CREATE INDEX idx_category_event_stream_aggregate_id ON category_event_stream (aggregate_id)');
        $this->addSql('CREATE INDEX idx_category_event_stream_created_at ON category_event_stream (created_at)');
        $this->addSql('CREATE UNIQUE INDEX uniq_category_event_stream_event_id ON category_event_stream (event_id)');
        $this->addSql('COMMENT ON COLUMN category_event_stream.event_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\EventId)\'');
        $this->addSql('COMMENT ON COLUMN category_event_stream.aggregate_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\AggregateId)\'');
        $this->addSql('COMMENT ON COLUMN category_event_stream.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE category_translation (id BIGINT NOT NULL, category_id UUID NOT NULL, locale VARCHAR(255) NOT NULL, name TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3F2070412469DE2 ON category_translation (category_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_category_translation_category_id_locale ON category_translation (category_id, locale)');
        $this->addSql('COMMENT ON COLUMN category_translation.category_id IS \'(DC2Type:App\\Domain\\Category\\ValueObject\\CategoryId)\'');
        $this->addSql('COMMENT ON COLUMN category_translation.locale IS \'(DC2Type:App\\Domain\\Shared\\ValueObject\\Locale)\'');
        $this->addSql('COMMENT ON COLUMN category_translation.name IS \'(DC2Type:App\\Domain\\Category\\ValueObject\\Name)\'');
        $this->addSql('ALTER TABLE category_translation ADD CONSTRAINT FK_3F2070412469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category_translation DROP CONSTRAINT FK_3F2070412469DE2');
        $this->addSql('DROP SEQUENCE category_translation_id_seq CASCADE');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_event_stream');
        $this->addSql('DROP TABLE category_translation');
    }
}
