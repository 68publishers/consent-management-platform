<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221101003605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds the table "consent_statistics_projection".';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE consent_statistics_projection (id BIGSERIAL NOT NULL, project_id UUID NOT NULL, consent_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, positive_count INT NOT NULL, negative_count INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_csp_consent_id ON consent_statistics_projection (consent_id)');
        $this->addSql('CREATE INDEX idx_csp_project_id_created_at ON consent_statistics_projection (project_id, created_at)');
        $this->addSql('CREATE INDEX idx_csp_project_id_consent_id_created_at_include ON consent_statistics_projection (project_id, consent_id, created_at DESC) INCLUDE (positive_count, negative_count)');
        $this->addSql('COMMENT ON COLUMN consent_statistics_projection.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE consent_statistics_projection');
    }
}
