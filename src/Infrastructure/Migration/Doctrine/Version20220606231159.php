<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220606231159 extends AbstractMigration
{
	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return 'Adds tables for cookie providers and their translations.';
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function up(Schema $schema): void
	{
		$this->addSql('CREATE SEQUENCE cookie_provider_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE cookie_provider (id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, code VARCHAR(70) NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, link TEXT NOT NULL, version INT NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX idx_cookie_provider_created_at ON cookie_provider (created_at)');
		$this->addSql('CREATE UNIQUE INDEX uniq_cookie_provider_code ON cookie_provider (lower(code)) WHERE deleted_at IS NULL');
		$this->addSql('COMMENT ON COLUMN cookie_provider.id IS \'(DC2Type:App\\Domain\\CookieProvider\\ValueObject\\CookieProviderId)\'');
		$this->addSql('COMMENT ON COLUMN cookie_provider.created_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('COMMENT ON COLUMN cookie_provider.deleted_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('COMMENT ON COLUMN cookie_provider.code IS \'(DC2Type:App\\Domain\\CookieProvider\\ValueObject\\Code)\'');
		$this->addSql('COMMENT ON COLUMN cookie_provider.type IS \'(DC2Type:App\\Domain\\CookieProvider\\ValueObject\\ProviderType)\'');
		$this->addSql('COMMENT ON COLUMN cookie_provider.name IS \'(DC2Type:App\\Domain\\CookieProvider\\ValueObject\\Name)\'');
		$this->addSql('COMMENT ON COLUMN cookie_provider.link IS \'(DC2Type:App\\Domain\\CookieProvider\\ValueObject\\Link)\'');
		$this->addSql('CREATE TABLE cookie_provider_event_stream (id BIGSERIAL NOT NULL, event_id UUID NOT NULL, aggregate_id UUID NOT NULL, event_name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, parameters JSON NOT NULL, metadata JSONB NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX idx_cookie_provider_event_stream_event_id ON cookie_provider_event_stream (event_id)');
		$this->addSql('CREATE INDEX idx_cookie_provider_event_stream_aggregate_id ON cookie_provider_event_stream (aggregate_id)');
		$this->addSql('CREATE INDEX idx_cookie_provider_event_stream_created_at ON cookie_provider_event_stream (created_at)');
		$this->addSql('CREATE UNIQUE INDEX uniq_cookie_provider_event_stream_event_id ON cookie_provider_event_stream (event_id)');
		$this->addSql('COMMENT ON COLUMN cookie_provider_event_stream.event_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\EventId)\'');
		$this->addSql('COMMENT ON COLUMN cookie_provider_event_stream.aggregate_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\AggregateId)\'');
		$this->addSql('COMMENT ON COLUMN cookie_provider_event_stream.created_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('CREATE TABLE cookie_provider_translation (id BIGINT NOT NULL, cookie_provider_id UUID NOT NULL, locale VARCHAR(255) NOT NULL, purpose TEXT NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX IDX_D28E5119EA3C721B ON cookie_provider_translation (cookie_provider_id)');
		$this->addSql('CREATE UNIQUE INDEX uniq_cookie_provider_translation_cookie_provider_id_locale ON cookie_provider_translation (cookie_provider_id, locale)');
		$this->addSql('COMMENT ON COLUMN cookie_provider_translation.cookie_provider_id IS \'(DC2Type:App\\Domain\\CookieProvider\\ValueObject\\CookieProviderId)\'');
		$this->addSql('COMMENT ON COLUMN cookie_provider_translation.locale IS \'(DC2Type:App\\Domain\\Shared\\ValueObject\\Locale)\'');
		$this->addSql('COMMENT ON COLUMN cookie_provider_translation.purpose IS \'(DC2Type:App\\Domain\\CookieProvider\\ValueObject\\Purpose)\'');
		$this->addSql('ALTER TABLE cookie_provider_translation ADD CONSTRAINT FK_D28E5119EA3C721B FOREIGN KEY (cookie_provider_id) REFERENCES cookie_provider (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE cookie_provider_translation DROP CONSTRAINT FK_D28E5119EA3C721B');
		$this->addSql('DROP SEQUENCE cookie_provider_translation_id_seq CASCADE');
		$this->addSql('DROP TABLE cookie_provider');
		$this->addSql('DROP TABLE cookie_provider_event_stream');
		$this->addSql('DROP TABLE cookie_provider_translation');
	}
}
