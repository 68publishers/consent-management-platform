<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230929005442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds column "global_settings"."environment_settings".';
    }

    public function up(Schema $schema): void
    {
        $defaultEnvironmentsSettings = json_encode([
            'default_environment' => [
                'code' => 'default',
                'name' => 'Default',
                'color' => '#ffffff',
            ],
            'environments' => [],
        ]);

        $this->addSql('ALTER TABLE global_settings ADD environment_settings JSONB NOT NULL DEFAULT \'' . $defaultEnvironmentsSettings . '\'');
        $this->addSql('ALTER TABLE global_settings ALTER COLUMN environment_settings DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN global_settings.environment_settings IS \'(DC2Type:App\\Domain\\GlobalSettings\\ValueObject\\EnvironmentSettings)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE global_settings DROP environment_settings');
    }
}
