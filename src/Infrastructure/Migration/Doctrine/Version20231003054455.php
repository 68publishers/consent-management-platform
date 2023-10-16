<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231003054455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds column "consent_statistics_projection"."environment" and two new indexes.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE consent_statistics_projection ADD environment VARCHAR(255) NOT NULL DEFAULT \'default\'');
        $this->addSql('ALTER TABLE consent_statistics_projection ALTER COLUMN environment DROP DEFAULT');
        $this->addSql('CREATE INDEX idx_csp_project_id_environment_created_at ON consent_statistics_projection (project_id, environment, created_at)');
        $this->addSql('CREATE INDEX idx_csp_project_id_environment_consent_id_created_at_include ON consent_statistics_projection (project_id, environment, consent_id, created_at DESC) INCLUDE (positive_count, negative_count)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_csp_project_id_environment_created_at');
        $this->addSql('DROP INDEX idx_csp_project_id_environment_consent_id_created_at_include');
        $this->addSql('ALTER TABLE "consent_statistics_projection" DROP environment');
    }
}
