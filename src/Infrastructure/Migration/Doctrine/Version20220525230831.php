<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220525230831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds column user.deleted_at (soft-delete) and changed unique indexes for username and email_address (both case-insensitive).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX uniq_8d93d649b08e074e');
        $this->addSql('DROP INDEX uniq_8d93d649f85e0677');
        $this->addSql('ALTER TABLE "user" ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN "user".deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE UNIQUE INDEX uniq_user_username ON "user" (lower(username)) WHERE deleted_at IS NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_user_email_address ON "user" (lower(email_address)) WHERE deleted_at IS NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX uniq_user_username');
        $this->addSql('DROP INDEX uniq_user_email_address');
        $this->addSql('ALTER TABLE "user" DROP deleted_at');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649b08e074e ON "user" (email_address)');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649f85e0677 ON "user" (username)');
    }
}
