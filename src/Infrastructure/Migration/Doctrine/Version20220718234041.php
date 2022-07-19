<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220718234041 extends AbstractMigration
{
	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return 'Adds notification preferences into users table.';
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function up(Schema $schema): void
	{
		$this->addSql('ALTER TABLE "user" ADD notification_preferences JSONB NOT NULL DEFAULT \'[]\'');
		$this->addSql('COMMENT ON COLUMN "user".notification_preferences IS \'(DC2Type:App\\Domain\\User\\ValueObject\\NotificationPreferences)\'');
		$this->addSql('ALTER TABLE "user" ALTER COLUMN notification_preferences DROP DEFAULT');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE "user" DROP notification_preferences');
	}
}
