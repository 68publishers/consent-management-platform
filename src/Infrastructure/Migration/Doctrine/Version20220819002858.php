<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220819002858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds columns "cache_control_directives" and "use_entity_tag" into the table "global_settings".';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE global_settings ADD cache_control_directives JSON NOT NULL DEFAULT \'[]\'');
        $this->addSql('ALTER TABLE global_settings ADD use_entity_tag BOOLEAN NOT NULL DEFAULT FALSE');
        $this->addSql('ALTER TABLE global_settings ALTER COLUMN cache_control_directives DROP DEFAULT');
        $this->addSql('ALTER TABLE global_settings ALTER COLUMN use_entity_tag DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE global_settings DROP cache_control_directives');
        $this->addSql('ALTER TABLE global_settings DROP use_entity_tag');
    }
}
