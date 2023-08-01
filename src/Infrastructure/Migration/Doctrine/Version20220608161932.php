<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220608161932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds column "profile_locale" into the table "user".';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" ADD profile_locale VARCHAR(255) NOT NULL DEFAULT \'cs\'');
        $this->addSql('COMMENT ON COLUMN "user".profile_locale IS \'(DC2Type:App\\Domain\\Shared\\ValueObject\\Locale)\'');
        $this->addSql('ALTER TABLE "user" ALTER COLUMN profile_locale DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" DROP profile_locale');
    }
}
