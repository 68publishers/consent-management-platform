<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221023220229 extends AbstractMigration
{
	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return 'Changes the type of columns "parameters" to JSONB for all event streams.';
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function up(Schema $schema): void
	{
		$this->addSql('ALTER TABLE category_event_stream ALTER COLUMN parameters TYPE jsonb USING parameters::text::jsonb');
		$this->addSql('ALTER TABLE consent_event_stream ALTER COLUMN parameters TYPE jsonb USING parameters::text::jsonb');
		$this->addSql('ALTER TABLE consent_settings_event_stream ALTER COLUMN parameters TYPE jsonb USING parameters::text::jsonb');
		$this->addSql('ALTER TABLE cookie_event_stream ALTER COLUMN parameters TYPE jsonb USING parameters::text::jsonb');
		$this->addSql('ALTER TABLE cookie_provider_event_stream ALTER COLUMN parameters TYPE jsonb USING parameters::text::jsonb');
		$this->addSql('ALTER TABLE global_settings_event_stream ALTER COLUMN parameters TYPE jsonb USING parameters::text::jsonb');
		$this->addSql('ALTER TABLE import_event_stream ALTER COLUMN parameters TYPE jsonb USING parameters::text::jsonb');
		$this->addSql('ALTER TABLE password_request_event_stream ALTER COLUMN parameters TYPE jsonb USING parameters::text::jsonb');
		$this->addSql('ALTER TABLE project_event_stream ALTER COLUMN parameters TYPE jsonb USING parameters::text::jsonb');
		$this->addSql('ALTER TABLE user_event_stream ALTER COLUMN parameters TYPE jsonb USING parameters::text::jsonb');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE category_event_stream ALTER COLUMN parameters TYPE json USING parameters::text::json');
		$this->addSql('ALTER TABLE consent_event_stream ALTER COLUMN parameters TYPE json USING parameters::text::json');
		$this->addSql('ALTER TABLE consent_settings_event_stream ALTER COLUMN parameters TYPE json USING parameters::text::json');
		$this->addSql('ALTER TABLE cookie_event_stream ALTER COLUMN parameters TYPE json USING parameters::text::json');
		$this->addSql('ALTER TABLE cookie_provider_event_stream ALTER COLUMN parameters TYPE json USING parameters::text::json');
		$this->addSql('ALTER TABLE global_settings_event_stream ALTER COLUMN parameters TYPE json USING parameters::text::json');
		$this->addSql('ALTER TABLE import_event_stream ALTER COLUMN parameters TYPE json USING parameters::text::json');
		$this->addSql('ALTER TABLE password_request_event_stream ALTER COLUMN parameters TYPE json USING parameters::text::json');
		$this->addSql('ALTER TABLE project_event_stream ALTER COLUMN parameters TYPE json USING parameters::text::json');
		$this->addSql('ALTER TABLE user_event_stream ALTER COLUMN parameters TYPE json USING parameters::text::json');
	}
}
