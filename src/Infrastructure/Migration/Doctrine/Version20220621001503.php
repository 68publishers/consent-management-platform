<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use DateTimeZone;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use App\Domain\CookieProvider\ValueObject\ProviderType;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;

final class Version20220621001503 extends AbstractMigration
{
	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return 'Adds a column "private" into the table "cookie_provider" and a column "cookie_provider_id" into the table "project".';
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 * @throws \Doctrine\DBAL\Exception
	 */
	public function up(Schema $schema): void
	{
		$this->addSql('ALTER TABLE cookie_provider ADD private BOOLEAN NOT NULL DEFAULT FALSE');
		$this->addSql('ALTER TABLE cookie_provider ALTER COLUMN private DROP DEFAULT');

		$this->addSql('ALTER TABLE project ADD cookie_provider_id UUID DEFAULT NULL');
		$this->addSql('COMMENT ON COLUMN project.cookie_provider_id IS \'(DC2Type:App\\Domain\\CookieProvider\\ValueObject\\CookieProviderId)\'');
		$this->addCookieProvidersToExistingProjects();
		$this->addSql('ALTER TABLE project ALTER COLUMN cookie_provider_id DROP DEFAULT');
		$this->addSql('ALTER TABLE project ALTER COLUMN cookie_provider_id SET NOT NULL');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE cookie_provider DROP "private"');
		$this->addSql('ALTER TABLE project DROP cookie_provider_id');
	}

	/**
	 * @return void
	 * @throws \Doctrine\DBAL\Exception
	 * @throws \Exception
	 */
	private function addCookieProvidersToExistingProjects(): void
	{
		$projects = $this->connection->createQueryBuilder()
			->select('p.id, p.code, p.name')
			->from('project', 'p')
			->executeQuery();

		while ($row = $projects->fetchAssociative()) {
			$cookieProviderId = CookieProviderId::new();
			$insertCookieProviderSql = $this->connection->createQueryBuilder()
				->insert('cookie_provider')
				->values([
					'id' => ':id',
					'created_at' => ':created_at',
					'code' => ':code',
					'type' => ':type',
					'name' => ':name',
					'link' => ':link',
					'private' => ':private',
					'version' => ':version',
				])
				->getSQL();

			$this->addSql($insertCookieProviderSql, [
				'id' => $cookieProviderId->toString(),
				'created_at' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format(DateTimeInterface::ATOM),
				'code' => $row['code'] . time(),
				'type' => ProviderType::FIRST_PARTY,
				'name' => $row['name'],
				'link' => 'https://www.example.com',
				'private' => TRUE,
				'version' => 1,
			], [
				'guid',
				'datetime_immutable',
				'string',
				'string',
				'string',
				'string',
				'boolean',
				'integer',
			]);

			$updateProjectSql = $this->connection->createQueryBuilder()
				->update('project')
				->set('cookie_provider_id', ':cookieProviderId')
				->where('id = :projectId')
				->getSQL();

			$this->addSql($updateProjectSql, [
				'cookieProviderId' => $cookieProviderId->toString(),
				'projectId' => $row['id'],
			], [
				'guid',
				'guid',
			]);
		}
	}
}
