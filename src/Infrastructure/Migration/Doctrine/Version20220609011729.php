<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220609011729 extends AbstractMigration
{
	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return 'Adds column "timezone" into tables "user" and "project".';
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function up(Schema $schema): void
	{
		$this->addSql('ALTER TABLE "user" ADD timezone VARCHAR(255) NOT NULL DEFAULT \'Europe/Prague\'');
		$this->addSql('COMMENT ON COLUMN "user".timezone IS \'(DC2Type:datetime_zone)\'');
		$this->addSql('ALTER TABLE project ADD timezone VARCHAR(255) NOT NULL DEFAULT \'Europe/Prague\'');
		$this->addSql('COMMENT ON COLUMN project.timezone IS \'(DC2Type:datetime_zone)\'');
		$this->addSql('ALTER TABLE "user" ALTER COLUMN timezone DROP DEFAULT');
		$this->addSql('ALTER TABLE project ALTER COLUMN timezone DROP DEFAULT');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE project DROP timezone');
		$this->addSql('ALTER TABLE "user" DROP timezone');
	}
}
