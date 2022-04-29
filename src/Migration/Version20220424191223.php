<?php

declare(strict_types=1);

namespace App\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220424191223 extends AbstractMigration
{
	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return 'Initializes table for password requests.';
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function up(Schema $schema): void
	{
		$this->addSql('CREATE TABLE password_request (id UUID NOT NULL, user_id UUID NOT NULL, status VARCHAR(255) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expired_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, finished_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, version INT NOT NULL, request_ip_address VARCHAR(255) NOT NULL, request_user_agent TEXT NOT NULL, finished_ip_address VARCHAR(255) NOT NULL, finished_user_agent TEXT NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX idx_password_request_requested_at ON password_request (requested_at)');
		$this->addSql('CREATE INDEX idx_password_request_user_id ON password_request (user_id)');
		$this->addSql('COMMENT ON COLUMN password_request.id IS \'(DC2Type:SixtyEightPublishers\\ForgotPasswordBundle\\Domain\\Dto\\PasswordRequestId)\'');
		$this->addSql('COMMENT ON COLUMN password_request.user_id IS \'(DC2Type:SixtyEightPublishers\\UserBundle\\Domain\\Dto\\UserId)\'');
		$this->addSql('COMMENT ON COLUMN password_request.status IS \'(DC2Type:SixtyEightPublishers\\ForgotPasswordBundle\\Domain\\Dto\\Status)\'');
		$this->addSql('COMMENT ON COLUMN password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('COMMENT ON COLUMN password_request.expired_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('COMMENT ON COLUMN password_request.finished_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('COMMENT ON COLUMN password_request.request_ip_address IS \'(DC2Type:SixtyEightPublishers\\ForgotPasswordBundle\\Domain\\Dto\\IpAddress)\'');
		$this->addSql('COMMENT ON COLUMN password_request.request_user_agent IS \'(DC2Type:SixtyEightPublishers\\ForgotPasswordBundle\\Domain\\Dto\\UserAgent)\'');
		$this->addSql('COMMENT ON COLUMN password_request.finished_ip_address IS \'(DC2Type:SixtyEightPublishers\\ForgotPasswordBundle\\Domain\\Dto\\IpAddress)\'');
		$this->addSql('COMMENT ON COLUMN password_request.finished_user_agent IS \'(DC2Type:SixtyEightPublishers\\ForgotPasswordBundle\\Domain\\Dto\\UserAgent)\'');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function down(Schema $schema): void
	{
		$this->addSql('DROP TABLE password_request');
	}
}
