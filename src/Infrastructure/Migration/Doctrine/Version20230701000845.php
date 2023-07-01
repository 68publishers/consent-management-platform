<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Types\Types;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230701000845 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Adds read model table "project_cookie_suggestion_statistics" and creates empty rows for existing projects.';
	}

	public function up(Schema $schema): void
	{
		$this->addSql('CREATE TABLE project_cookie_suggestion_statistics (id BIGSERIAL NOT NULL, project_id UUID NOT NULL, missing INT NOT NULL, unassociated INT NOT NULL, problematic INT NOT NULL, unproblematic INT NOT NULL, ignored INT NOT NULL, total INT NOT NULL, total_without_virtual INT NOT NULL, latest_found_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE UNIQUE INDEX uniq_pcss_project_id ON project_cookie_suggestion_statistics (project_id)');
		$this->addSql('CREATE INDEX idx_pcss_project_id_latest_found_at ON project_cookie_suggestion_statistics (project_id, latest_found_at)');
		$this->addSql('COMMENT ON COLUMN project_cookie_suggestion_statistics.latest_found_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('ALTER TABLE project_cookie_suggestion_statistics ADD CONSTRAINT FK_D2B8AA0A166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

		$projectRows = $this->connection->createQueryBuilder()
			->select('p.id')
			->from('project', 'p')
			->where('p.deleted_at IS NULL')
			->fetchAllAssociative();

		foreach ($projectRows as $projectRow) {
			$insertSql = $this->connection->createQueryBuilder()
				->insert('project_cookie_suggestion_statistics')
				->values([
					'project_id' => ':projectId',
					'missing' => 0,
					'unassociated' => 0,
					'problematic' => 0,
					'unproblematic' => 0,
					'ignored' => 0,
					'total' => 0,
					'total_without_virtual' => 0,
				])
				->getSQL();

			$this->addSql(
				$insertSql,
				[
					'projectId' => $projectRow['id'],
				],
				[
					'projectId' => Types::GUID,
				]
			);
		}
	}

	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE project_cookie_suggestion_statistics DROP CONSTRAINT FK_D2B8AA0A166D1F9C');
		$this->addSql('DROP TABLE project_cookie_suggestion_statistics');
	}
}
