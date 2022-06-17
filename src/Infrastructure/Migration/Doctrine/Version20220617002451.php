<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220617002451 extends AbstractMigration
{
	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return 'Added a column "deleted_at" into projects table.';
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function up(Schema $schema): void
	{
		$this->addSql('DROP INDEX uniq_2fb3d0ee77153098');
		$this->addSql('ALTER TABLE project ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('COMMENT ON COLUMN project.deleted_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('CREATE UNIQUE INDEX uniq_project_code ON project (code) WHERE deleted_at IS NULL');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function down(Schema $schema): void
	{
		$this->addSql('DROP INDEX uniq_project_code');
		$this->addSql('ALTER TABLE project DROP deleted_at');
		$this->addSql('CREATE UNIQUE INDEX uniq_2fb3d0ee77153098 ON project (code)');
	}
}
