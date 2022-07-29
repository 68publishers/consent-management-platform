<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220728231454 extends AbstractMigration
{
	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return 'Adds column "warned" into the table "import".';
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function up(Schema $schema): void
	{
		$this->addSql('ALTER TABLE import ADD warned INT NOT NULL DEFAULT 0');
		$this->addSql('COMMENT ON COLUMN import.warned IS \'(DC2Type:App\\Domain\\Import\\ValueObject\\Total)\'');
		$this->addSql('ALTER TABLE import ALTER COLUMN warned DROP DEFAULT');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE import DROP warned');
	}
}
