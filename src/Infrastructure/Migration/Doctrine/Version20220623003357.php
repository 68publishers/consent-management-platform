<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220623003357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds translations table for projects.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE project_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE project_translation (id BIGINT NOT NULL, project_id UUID NOT NULL, locale VARCHAR(255) NOT NULL, template TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CA6B294166D1F9C ON project_translation (project_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_project_translation_project_id_locale ON project_translation (project_id, locale)');
        $this->addSql('COMMENT ON COLUMN project_translation.project_id IS \'(DC2Type:App\\Domain\\Project\\ValueObject\\ProjectId)\'');
        $this->addSql('COMMENT ON COLUMN project_translation.locale IS \'(DC2Type:App\\Domain\\Shared\\ValueObject\\Locale)\'');
        $this->addSql('COMMENT ON COLUMN project_translation.template IS \'(DC2Type:App\\Domain\\Project\\ValueObject\\Template)\'');
        $this->addSql('ALTER TABLE project_translation ADD CONSTRAINT FK_7CA6B294166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE project_translation_id_seq CASCADE');
        $this->addSql('DROP TABLE project_translation');
    }
}
