<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231001222127 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds column "project"."environments".';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project ADD environments JSONB NOT NULL DEFAULT \'[]\'');
        $this->addSql('ALTER TABLE project ALTER COLUMN environments DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN project.environments IS \'(DC2Type:App\\Domain\\Project\\ValueObject\\Environments)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project DROP environments');
    }
}
