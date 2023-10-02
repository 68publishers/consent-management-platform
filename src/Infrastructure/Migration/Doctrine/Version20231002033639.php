<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231002033639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds columns "cookie"."all_environments" and "cookie"."environments".';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cookie ADD all_environments BOOLEAN NOT NULL DEFAULT TRUE');
        $this->addSql('ALTER TABLE cookie ALTER COLUMN all_environments DROP DEFAULT');
        $this->addSql('ALTER TABLE cookie ADD environments JSONB NOT NULL DEFAULT \'[]\'');
        $this->addSql('ALTER TABLE cookie ALTER COLUMN environments DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN cookie.environments IS \'(DC2Type:App\\Domain\\Cookie\\ValueObject\\Environments)\'');
        $this->addSql('CREATE INDEX idx_cookie_environments ON cookie USING gin (environments jsonb_path_ops)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_cookie_environments');
        $this->addSql('ALTER TABLE cookie DROP all_environments');
        $this->addSql('ALTER TABLE cookie DROP environments');
    }
}
