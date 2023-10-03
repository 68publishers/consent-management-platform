<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231003054455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds column "consent_statistics_projection"."environment" and four new indexes.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE consent_statistics_projection ADD environment VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_csp_project_id_environment_created_at ON consent_statistics_projection (project_id, environment, created_at)');
        $this->addSql('CREATE INDEX idx_csp_project_id_created_at_partial ON consent_statistics_projection (project_id, environment, created_at)  WHERE environment IS NULL');
        $this->addSql('CREATE INDEX idx_csp_project_id_environment_consent_id_created_at_include ON consent_statistics_projection (project_id, environment, consent_id, created_at DESC) INCLUDE (positive_count, negative_count)');
        $this->addSql('CREATE INDEX idx_csp_project_id_consent_id_created_at_include_partial ON consent_statistics_projection (project_id, consent_id, created_at DESC) INCLUDE (positive_count, negative_count)  WHERE environment IS NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_csp_project_id_environment_created_at');
        $this->addSql('DROP INDEX idx_csp_project_id_created_at_partial');
        $this->addSql('DROP INDEX idx_csp_project_id_environment_consent_id_created_at_include');
        $this->addSql('DROP INDEX idx_csp_project_id_consent_id_created_at_include_partial');
        $this->addSql('ALTER TABLE "consent_statistics_projection" DROP environment');
    }
}
