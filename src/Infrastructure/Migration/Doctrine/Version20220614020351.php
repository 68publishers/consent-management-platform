<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220614020351 extends AbstractMigration
{
	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return 'Adds tables for cookies and their translations.';
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function up(Schema $schema): void
	{
		$this->addSql('CREATE SEQUENCE cookie_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE cookie (id UUID NOT NULL, category_id UUID NOT NULL, cookie_provider_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, name VARCHAR(255) NOT NULL, version INT NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX idx_cookie_created_at ON cookie (created_at)');
		$this->addSql('CREATE INDEX idx_cookie_category_id ON cookie (category_id)');
		$this->addSql('CREATE INDEX idx_cookie_cookie_provider_id ON cookie (cookie_provider_id)');
		$this->addSql('COMMENT ON COLUMN cookie.id IS \'(DC2Type:App\\Domain\\Cookie\\ValueObject\\CookieId)\'');
		$this->addSql('COMMENT ON COLUMN cookie.category_id IS \'(DC2Type:App\\Domain\\Category\\ValueObject\\CategoryId)\'');
		$this->addSql('COMMENT ON COLUMN cookie.cookie_provider_id IS \'(DC2Type:App\\Domain\\CookieProvider\\ValueObject\\CookieProviderId)\'');
		$this->addSql('COMMENT ON COLUMN cookie.created_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('COMMENT ON COLUMN cookie.deleted_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('COMMENT ON COLUMN cookie.name IS \'(DC2Type:App\\Domain\\Cookie\\ValueObject\\Name)\'');
		$this->addSql('CREATE TABLE cookie_event_stream (id BIGSERIAL NOT NULL, event_id UUID NOT NULL, aggregate_id UUID NOT NULL, event_name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, parameters JSON NOT NULL, metadata JSONB NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX idx_cookie_event_stream_event_id ON cookie_event_stream (event_id)');
		$this->addSql('CREATE INDEX idx_cookie_event_stream_aggregate_id ON cookie_event_stream (aggregate_id)');
		$this->addSql('CREATE INDEX idx_cookie_event_stream_created_at ON cookie_event_stream (created_at)');
		$this->addSql('CREATE UNIQUE INDEX uniq_cookie_event_stream_event_id ON cookie_event_stream (event_id)');
		$this->addSql('COMMENT ON COLUMN cookie_event_stream.event_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\EventId)\'');
		$this->addSql('COMMENT ON COLUMN cookie_event_stream.aggregate_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\AggregateId)\'');
		$this->addSql('COMMENT ON COLUMN cookie_event_stream.created_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('CREATE TABLE cookie_translation (id BIGINT NOT NULL, cookie_id UUID NOT NULL, locale VARCHAR(255) NOT NULL, purpose TEXT NOT NULL, processing_time VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX IDX_54069FDE943BD662 ON cookie_translation (cookie_id)');
		$this->addSql('CREATE UNIQUE INDEX uniq_cookie_translation_cookie_id_locale ON cookie_translation (cookie_id, locale)');
		$this->addSql('COMMENT ON COLUMN cookie_translation.cookie_id IS \'(DC2Type:App\\Domain\\Cookie\\ValueObject\\CookieId)\'');
		$this->addSql('COMMENT ON COLUMN cookie_translation.locale IS \'(DC2Type:App\\Domain\\Shared\\ValueObject\\Locale)\'');
		$this->addSql('COMMENT ON COLUMN cookie_translation.purpose IS \'(DC2Type:App\\Domain\\Cookie\\ValueObject\\Purpose)\'');
		$this->addSql('COMMENT ON COLUMN cookie_translation.processing_time IS \'(DC2Type:App\\Domain\\Cookie\\ValueObject\\ProcessingTime)\'');
		$this->addSql('ALTER TABLE cookie_translation ADD CONSTRAINT FK_54069FDE943BD662 FOREIGN KEY (cookie_id) REFERENCES cookie (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE cookie_translation DROP CONSTRAINT FK_54069FDE943BD662');
		$this->addSql('DROP SEQUENCE cookie_translation_id_seq CASCADE');
		$this->addSql('DROP TABLE cookie');
		$this->addSql('DROP TABLE cookie_event_stream');
		$this->addSql('DROP TABLE cookie_translation');
	}
}
