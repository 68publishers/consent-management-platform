<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220914232016 extends AbstractMigration
{
	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return 'Adds the column "author_id" instead of the column "author" in the table "imports"';
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function up(Schema $schema): void
	{
		$this->addSql('ALTER TABLE import ADD author_id UUID DEFAULT NULL');
		$this->addSql('ALTER TABLE import DROP author');
		$this->addSql('COMMENT ON COLUMN import.author_id IS \'(DC2Type:SixtyEightPublishers\\UserBundle\\Domain\\ValueObject\\UserId)\'');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE import ADD author VARCHAR(255) NOT NULL DEFAULT \'system\'');
		$this->addSql('ALTER TABLE import ALTER COLUMN author DROP DEFAULT');
		$this->addSql('ALTER TABLE import DROP author_id');
		$this->addSql('COMMENT ON COLUMN import.author IS \'(DC2Type:App\\Domain\\Import\\ValueObject\\Author)\'');
	}
}
