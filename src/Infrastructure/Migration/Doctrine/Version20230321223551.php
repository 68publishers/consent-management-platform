<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230321223551 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Adds index over columns "consent.last_update_at" and "consent.project_id';
	}

	public function up(Schema $schema): void
	{
		$this->addSql('CREATE INDEX idx_consent_last_update_at_project_id ON consent (last_update_at, project_id)');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('DROP INDEX idx_consent_last_update_at_project_id');
	}
}
