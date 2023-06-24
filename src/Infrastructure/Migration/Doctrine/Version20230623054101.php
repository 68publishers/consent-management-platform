<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230623054101 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Adds column "cookie"."domain".';
	}

	public function up(Schema $schema): void
	{
		$this->addSql('ALTER TABLE cookie ADD domain VARCHAR(255) NOT NULL DEFAULT \'\'');
		$this->addSql('COMMENT ON COLUMN cookie.domain IS \'(DC2Type:App\\Domain\\Cookie\\ValueObject\\Domain)\'');
		$this->addSql('ALTER TABLE cookie ALTER COLUMN domain DROP DEFAULT');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE cookie DROP domain');
	}
}
