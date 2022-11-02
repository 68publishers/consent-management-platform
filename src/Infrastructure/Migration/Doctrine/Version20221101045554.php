<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221101045554 extends AbstractMigration
{
	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return 'Updates stored event of type "App\Domain\Consent\Event\ConsentUpdated" in the event stream "consent_event_stream" with the "project_id" field.';
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function up(Schema $schema): void
	{
		$this->addSql('
			UPDATE consent_event_stream es
			SET parameters = parameters || json_build_object(\'project_id\', p.id)::jsonb
			FROM consent c
			JOIN project p ON p.id = c.project_id
			WHERE es.aggregate_id = c.id AND es.event_name = \'App\Domain\Consent\Event\ConsentUpdated\'
		');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function down(Schema $schema): void
	{
		$this->addSql('
			UPDATE consent_event_stream es
			SET parameters = parameters - \'project_id\'
			WHERE es.event_name = \'App\Domain\Consent\Event\ConsentUpdated\'
		');
	}
}
