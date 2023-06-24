<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230622014445 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Adds tables "cookie_suggestion" (AR + event stream) and "cookie_occurrence".';
	}

	public function up(Schema $schema): void
	{
		$this->addSql('CREATE TABLE cookie_occurrence (id UUID NOT NULL, cookie_suggestion_id UUID NOT NULL, scenario_name VARCHAR(255) NOT NULL, found_on_url TEXT NOT NULL, accepted_categories JSONB NOT NULL, last_found_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX IDX_4CE2EF93FC3FC7E ON cookie_occurrence (cookie_suggestion_id)');
		$this->addSql('CREATE INDEX idx_cookie_suggestion_last_found_at ON cookie_occurrence (last_found_at)');
		$this->addSql('CREATE UNIQUE INDEX uniq_cookie_occurrence_cookie_suggestion_id_scenario_name ON cookie_occurrence (cookie_suggestion_id, scenario_name)');
		$this->addSql('COMMENT ON COLUMN cookie_occurrence.id IS \'(DC2Type:App\\Domain\\CookieSuggestion\\ValueObject\\CookieOccurrenceId)\'');
		$this->addSql('COMMENT ON COLUMN cookie_occurrence.cookie_suggestion_id IS \'(DC2Type:App\\Domain\\CookieSuggestion\\ValueObject\\CookieSuggestionId)\'');
		$this->addSql('COMMENT ON COLUMN cookie_occurrence.scenario_name IS \'(DC2Type:App\\Domain\\CookieSuggestion\\ValueObject\\ScenarioName)\'');
		$this->addSql('COMMENT ON COLUMN cookie_occurrence.found_on_url IS \'(DC2Type:App\\Domain\\CookieSuggestion\\ValueObject\\FoundOnUrl)\'');
		$this->addSql('COMMENT ON COLUMN cookie_occurrence.accepted_categories IS \'(DC2Type:App\\Domain\\CookieSuggestion\\ValueObject\\AcceptedCategories)\'');
		$this->addSql('COMMENT ON COLUMN cookie_occurrence.last_found_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('CREATE TABLE cookie_suggestion (id UUID NOT NULL, project_id UUID NOT NULL, name VARCHAR(255) NOT NULL, domain VARCHAR(255) NOT NULL, version INT NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX idx_cookie_suggestion_project_id ON cookie_suggestion (project_id)');
		$this->addSql('CREATE UNIQUE INDEX uniq_cookie_suggestion_project_id_name_domain ON cookie_suggestion (project_id, name, domain)');
		$this->addSql('COMMENT ON COLUMN cookie_suggestion.id IS \'(DC2Type:App\\Domain\\CookieSuggestion\\ValueObject\\CookieSuggestionId)\'');
		$this->addSql('COMMENT ON COLUMN cookie_suggestion.project_id IS \'(DC2Type:App\\Domain\\Project\\ValueObject\\ProjectId)\'');
		$this->addSql('COMMENT ON COLUMN cookie_suggestion.name IS \'(DC2Type:App\\Domain\\CookieSuggestion\\ValueObject\\Name)\'');
		$this->addSql('COMMENT ON COLUMN cookie_suggestion.domain IS \'(DC2Type:App\\Domain\\CookieSuggestion\\ValueObject\\Domain)\'');
		$this->addSql('CREATE TABLE cookie_suggestion_event_stream (id BIGSERIAL NOT NULL, event_id UUID NOT NULL, aggregate_id UUID NOT NULL, event_name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, parameters JSONB NOT NULL, metadata JSONB NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX idx_cookie_suggestion_event_stream_event_id ON cookie_suggestion_event_stream (event_id)');
		$this->addSql('CREATE INDEX idx_cookie_suggestion_event_stream_aggregate_id ON cookie_suggestion_event_stream (aggregate_id)');
		$this->addSql('CREATE INDEX idx_cookie_suggestion_event_stream_created_at ON cookie_suggestion_event_stream (created_at)');
		$this->addSql('CREATE UNIQUE INDEX uniq_cookie_suggestion_event_stream_event_id ON cookie_suggestion_event_stream (event_id)');
		$this->addSql('COMMENT ON COLUMN cookie_suggestion_event_stream.event_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\EventId)\'');
		$this->addSql('COMMENT ON COLUMN cookie_suggestion_event_stream.aggregate_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\AggregateId)\'');
		$this->addSql('COMMENT ON COLUMN cookie_suggestion_event_stream.created_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('ALTER TABLE cookie_occurrence ADD CONSTRAINT FK_4CE2EF93FC3FC7E FOREIGN KEY (cookie_suggestion_id) REFERENCES cookie_suggestion (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE cookie_occurrence DROP CONSTRAINT FK_4CE2EF93FC3FC7E');
		$this->addSql('DROP TABLE cookie_occurrence');
		$this->addSql('DROP TABLE cookie_suggestion');
		$this->addSql('DROP TABLE cookie_suggestion_event_stream');
	}
}
