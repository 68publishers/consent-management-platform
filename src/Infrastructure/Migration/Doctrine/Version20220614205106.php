<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220614205106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Moves the column "processing_time" from the "cookie_translation" table to the "cookie" table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cookie ADD processing_time VARCHAR(255) NOT NULL DEFAULT \'persistent\'');
        $this->addSql('COMMENT ON COLUMN cookie.processing_time IS \'(DC2Type:App\\Domain\\Cookie\\ValueObject\\ProcessingTime)\'');
        $this->addSql('ALTER TABLE cookie ALTER COLUMN processing_time DROP DEFAULT');
        $this->addSql('ALTER TABLE cookie_translation DROP processing_time');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cookie DROP processing_time');
        $this->addSql('ALTER TABLE cookie_translation ADD processing_time VARCHAR(255) NOT NULL');
        $this->addSql('COMMENT ON COLUMN cookie_translation.processing_time IS \'(DC2Type:App\\Domain\\Cookie\\ValueObject\\ProcessingTime)\'');
    }
}
