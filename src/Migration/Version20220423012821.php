<?php

declare(strict_types=1);

namespace App\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220423012821 extends AbstractMigration
{
	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return 'Initializes table for users.';
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function up(Schema $schema): void
	{
		$this->addSql('CREATE TABLE "user" (id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) DEFAULT NULL, email_address VARCHAR(255) NOT NULL, roles JSONB NOT NULL, version INT NOT NULL, firstname VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON "user" (username)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649B08E074E ON "user" (email_address)');
		$this->addSql('CREATE INDEX idx_user_created_at ON "user" (created_at)');
		$this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:SixtyEightPublishers\\UserBundle\\Domain\\Dto\\UserId)\'');
		$this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('COMMENT ON COLUMN "user".username IS \'(DC2Type:SixtyEightPublishers\\UserBundle\\Domain\\Dto\\Username)\'');
		$this->addSql('COMMENT ON COLUMN "user".password IS \'(DC2Type:SixtyEightPublishers\\UserBundle\\Domain\\Dto\\HashedPassword)\'');
		$this->addSql('COMMENT ON COLUMN "user".email_address IS \'(DC2Type:SixtyEightPublishers\\UserBundle\\Domain\\Dto\\EmailAddress)\'');
		$this->addSql('COMMENT ON COLUMN "user".roles IS \'(DC2Type:SixtyEightPublishers\\UserBundle\\Domain\\Dto\\Roles)\'');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function down(Schema $schema): void
	{
		$this->addSql('DROP TABLE "user"');
	}
}
