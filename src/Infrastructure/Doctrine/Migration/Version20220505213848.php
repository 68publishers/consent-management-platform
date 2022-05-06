<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220505213848 extends AbstractMigration
{
	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return 'Initializes tables for users and password requests.';
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
		$this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:SixtyEightPublishers\\UserBundle\\Domain\\ValueObject\\UserId)\'');
		$this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('COMMENT ON COLUMN "user".username IS \'(DC2Type:SixtyEightPublishers\\UserBundle\\Domain\\ValueObject\\Username)\'');
		$this->addSql('COMMENT ON COLUMN "user".password IS \'(DC2Type:SixtyEightPublishers\\UserBundle\\Domain\\ValueObject\\HashedPassword)\'');
		$this->addSql('COMMENT ON COLUMN "user".email_address IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\EmailAddress)\'');
		$this->addSql('COMMENT ON COLUMN "user".roles IS \'(DC2Type:SixtyEightPublishers\\UserBundle\\Domain\\ValueObject\\Roles)\'');
		$this->addSql('CREATE TABLE password_request (id UUID NOT NULL, email_address VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expired_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, finished_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, version INT NOT NULL, request_ip_address VARCHAR(255) NOT NULL, request_user_agent TEXT NOT NULL, finished_ip_address VARCHAR(255) NOT NULL, finished_user_agent TEXT NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX idx_password_request_requested_at ON password_request (requested_at)');
		$this->addSql('CREATE INDEX idx_password_request_email_address ON password_request (email_address)');
		$this->addSql('COMMENT ON COLUMN password_request.id IS \'(DC2Type:SixtyEightPublishers\\ForgotPasswordBundle\\Domain\\ValueObject\\PasswordRequestId)\'');
		$this->addSql('COMMENT ON COLUMN password_request.email_address IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\EmailAddress)\'');
		$this->addSql('COMMENT ON COLUMN password_request.status IS \'(DC2Type:SixtyEightPublishers\\ForgotPasswordBundle\\Domain\\ValueObject\\Status)\'');
		$this->addSql('COMMENT ON COLUMN password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('COMMENT ON COLUMN password_request.expired_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('COMMENT ON COLUMN password_request.finished_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('COMMENT ON COLUMN password_request.request_ip_address IS \'(DC2Type:SixtyEightPublishers\\ForgotPasswordBundle\\Domain\\ValueObject\\IpAddress)\'');
		$this->addSql('COMMENT ON COLUMN password_request.request_user_agent IS \'(DC2Type:SixtyEightPublishers\\ForgotPasswordBundle\\Domain\\ValueObject\\UserAgent)\'');
		$this->addSql('COMMENT ON COLUMN password_request.finished_ip_address IS \'(DC2Type:SixtyEightPublishers\\ForgotPasswordBundle\\Domain\\ValueObject\\IpAddress)\'');
		$this->addSql('COMMENT ON COLUMN password_request.finished_user_agent IS \'(DC2Type:SixtyEightPublishers\\ForgotPasswordBundle\\Domain\\ValueObject\\UserAgent)\'');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function down(Schema $schema): void
	{
		$this->addSql('DROP TABLE "user"');
		$this->addSql('DROP TABLE password_request');
	}
}
