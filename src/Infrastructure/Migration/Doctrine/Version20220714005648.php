<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220714005648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Drops column project.timezone.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project DROP timezone');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project ADD timezone VARCHAR(255) NOT NULL DEFAULT \'Europe/Prague\'');
        $this->addSql('COMMENT ON COLUMN project.timezone IS \'(DC2Type:datetime_zone)\'');
        $this->addSql('ALTER TABLE project ALTER COLUMN timezone DROP DEFAULT');
    }
}
