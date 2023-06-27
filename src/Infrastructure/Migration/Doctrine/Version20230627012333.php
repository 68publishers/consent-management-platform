<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use DateTimeZone;
use DateTimeImmutable;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Types;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230627012333 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Adds column "cookie_suggestion"."created_at" and fills existing row with the current timestamp + 1 second for each row.';
	}

	/**
	 * @throws Exception
	 */
	public function up(Schema $schema): void
	{
		$this->addSql('ALTER TABLE cookie_suggestion ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('COMMENT ON COLUMN cookie_suggestion.created_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('CREATE INDEX idx_cookie_suggestion_created_at ON cookie_suggestion (created_at)');

		$existingCookieSuggestionRows = $this->connection->createQueryBuilder()
			->select('cs.id')
			->from('cookie_suggestion', 'cs')
			->fetchAllAssociative();

		$now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

		foreach ($existingCookieSuggestionRows as $existingCookieSuggestionRow) {
			$now = $now->modify('+1 second');

			$updateSql = $this->connection->createQueryBuilder()
				->update('cookie_suggestion')
				->set('created_at', ':createdAt')
				->where('id = :id')
				->getSQL();

			$this->addSql(
				$updateSql,
				[
					'createdAt' => $now,
					'id' => $existingCookieSuggestionRow['id'],
				],
				[
					'createdAt' => Types::DATETIME_IMMUTABLE,
					'id' => Types::GUID,
				],
			);
		}

		$this->addSql('ALTER TABLE cookie_suggestion ALTER COLUMN created_at DROP DEFAULT');
		$this->addSql('ALTER TABLE cookie_suggestion ALTER COLUMN created_at SET NOT NULL');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('DROP INDEX idx_cookie_suggestion_created_at');
		$this->addSql('ALTER TABLE cookie_suggestion DROP created_at');
	}
}
