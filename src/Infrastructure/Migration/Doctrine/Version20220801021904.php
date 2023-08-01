<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220801021904 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds an index and a unique constraint over columns "name" and "cookie_provider_id" in the table "cookie".';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX idx_cookie_name_cookie_provider_id ON cookie (name, cookie_provider_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_cookie_name_cookie_provider_id ON cookie (name, cookie_provider_id) WHERE (deleted_at IS NULL)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_cookie_name_cookie_provider_id');
        $this->addSql('DROP INDEX uniq_cookie_name_cookie_provider_id');
    }
}
