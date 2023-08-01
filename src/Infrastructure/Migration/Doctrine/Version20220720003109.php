<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220720003109 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds column short_identifier into the consent settings table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE app_consent_settings_short_identifier INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE consent_settings ADD short_identifier INT NOT NULL DEFAULT nextval(\'app_consent_settings_short_identifier\')');
        $this->addSql('COMMENT ON COLUMN consent_settings.short_identifier IS \'(DC2Type:App\\Domain\\ConsentSettings\\ValueObject\\ShortIdentifier)\'');
        $this->addSql('ALTER TABLE consent_settings ALTER COLUMN short_identifier DROP DEFAULT');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CA814C72CFED4FBF ON consent_settings (short_identifier)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE app_consent_settings_short_identifier');
        $this->addSql('DROP INDEX UNIQ_CA814C72CFED4FBF');
        $this->addSql('ALTER TABLE consent_settings DROP short_identifier');
    }
}
