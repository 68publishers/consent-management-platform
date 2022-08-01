<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220731211616 extends AbstractMigration
{
	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return 'Adds column "active" into tables "cookie" and "cookie_provider".';
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function up(Schema $schema): void
	{
		$this->addSql('ALTER TABLE cookie ADD active BOOLEAN NOT NULL DEFAULT TRUE');
		$this->addSql('ALTER TABLE cookie_provider ADD active BOOLEAN NOT NULL DEFAULT TRUE');
		$this->addSql('ALTER TABLE cookie ALTER COLUMN active DROP DEFAULT');
		$this->addSql('ALTER TABLE cookie_provider ALTER COLUMN active DROP DEFAULT');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE cookie DROP active');
		$this->addSql('ALTER TABLE cookie_provider DROP active');
	}
}
