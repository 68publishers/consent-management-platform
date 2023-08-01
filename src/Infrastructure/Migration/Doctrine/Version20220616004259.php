<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220616004259 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds table "project_has_cookie_provider".';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE project_has_cookie_provider_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE project_has_cookie_provider (id BIGINT NOT NULL, project_id UUID NOT NULL, cookie_provider_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_44319F0D166D1F9C ON project_has_cookie_provider (project_id)');
        $this->addSql('CREATE INDEX idx_project_has_cookie_provider_cookie_provider_id ON project_has_cookie_provider (cookie_provider_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_project_has_cookie_provider_project_id_cookie_provider_id ON project_has_cookie_provider (project_id, cookie_provider_id)');
        $this->addSql('COMMENT ON COLUMN project_has_cookie_provider.project_id IS \'(DC2Type:App\\Domain\\Project\\ValueObject\\ProjectId)\'');
        $this->addSql('COMMENT ON COLUMN project_has_cookie_provider.cookie_provider_id IS \'(DC2Type:App\\Domain\\CookieProvider\\ValueObject\\CookieProviderId)\'');
        $this->addSql('ALTER TABLE project_has_cookie_provider ADD CONSTRAINT FK_44319F0D166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE project_has_cookie_provider_id_seq CASCADE');
        $this->addSql('DROP TABLE project_has_cookie_provider');
    }
}
