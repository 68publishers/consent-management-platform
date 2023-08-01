<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220513160719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Removes NOT NULL definition from column consent.settings_checksum.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE consent ALTER settings_checksum DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE consent ALTER settings_checksum SET NOT NULL');
    }
}
