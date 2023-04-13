<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230413164543 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Adds an unique index over columns cookie.(name, cookie_provider_id, category_id) instead of cookie.(name, cookie_provider_id).';
	}

	public function up(Schema $schema): void
	{
		$this->addSql('DROP INDEX uniq_cookie_name_cookie_provider_id');
		$this->addSql('CREATE UNIQUE INDEX uniq_cookie_name_cookie_provider_id_category_id ON cookie (name, cookie_provider_id, category_id)  WHERE (deleted_at IS NULL)');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('DROP INDEX uniq_cookie_name_cookie_provider_id_category_id');
		$this->addSql('CREATE UNIQUE INDEX uniq_cookie_name_cookie_provider_id ON cookie (name, cookie_provider_id)  WHERE (deleted_at IS NULL)');
	}
}
