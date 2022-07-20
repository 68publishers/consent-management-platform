<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use App\Infrastructure\ConsentSettings\Doctrine\ShortIdentifierGenerator;

final class Version20220720003109 extends AbstractMigration
{
	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return 'Adds column short_identifier into the consent settings table.';
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function up(Schema $schema): void
	{
		$this->addSql('CREATE SEQUENCE ' . ShortIdentifierGenerator::SEQUENCE_NAME . ' INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('ALTER TABLE consent_settings ADD short_identifier INT NOT NULL DEFAULT nextval(\'' . ShortIdentifierGenerator::SEQUENCE_NAME . '\')');
		$this->addSql('COMMENT ON COLUMN consent_settings.short_identifier IS \'(DC2Type:App\\Domain\\ConsentSettings\\ValueObject\\ShortIdentifier)\'');
		$this->addSql('ALTER TABLE consent_settings ALTER COLUMN short_identifier DROP DEFAULT');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_CA814C72CFED4FBF ON consent_settings (short_identifier)');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function down(Schema $schema): void
	{
		$this->addSql('DROP SEQUENCE ' . ShortIdentifierGenerator::class);
		$this->addSql('DROP INDEX UNIQ_CA814C72CFED4FBF');
		$this->addSql('ALTER TABLE consent_settings DROP short_identifier');
	}
}
