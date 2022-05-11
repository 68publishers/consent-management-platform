<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220511001449 extends AbstractMigration
{
	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return 'Initializes table for projects and an association between projects and users.';
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function up(Schema $schema): void
	{
		$this->addSql('CREATE SEQUENCE user_has_project_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE project (id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(70) NOT NULL, color VARCHAR(255) NOT NULL, description TEXT NOT NULL, active BOOLEAN NOT NULL, version INT NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_2FB3D0EE77153098 ON project (code)');
		$this->addSql('CREATE INDEX idx_project_created_at ON project (created_at)');
		$this->addSql('COMMENT ON COLUMN project.id IS \'(DC2Type:App\\Domain\\Project\\ValueObject\\ProjectId)\'');
		$this->addSql('COMMENT ON COLUMN project.created_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('COMMENT ON COLUMN project.name IS \'(DC2Type:App\\Domain\\Project\\ValueObject\\Name)\'');
		$this->addSql('COMMENT ON COLUMN project.code IS \'(DC2Type:App\\Domain\\Project\\ValueObject\\Code)\'');
		$this->addSql('COMMENT ON COLUMN project.color IS \'(DC2Type:App\\Domain\\Project\\ValueObject\\Color)\'');
		$this->addSql('COMMENT ON COLUMN project.description IS \'(DC2Type:App\\Domain\\Project\\ValueObject\\Description)\'');
		$this->addSql('CREATE TABLE user_has_project (id INT NOT NULL, user_id UUID NOT NULL, project_id UUID NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX IDX_E15971A0A76ED395 ON user_has_project (user_id)');
		$this->addSql('CREATE INDEX idx_user_has_project_project_id ON user_has_project (project_id)');
		$this->addSql('CREATE UNIQUE INDEX uniq_user_has_project_user_id_project_id ON user_has_project (user_id, project_id)');
		$this->addSql('COMMENT ON COLUMN user_has_project.user_id IS \'(DC2Type:SixtyEightPublishers\\UserBundle\\Domain\\ValueObject\\UserId)\'');
		$this->addSql('COMMENT ON COLUMN user_has_project.project_id IS \'(DC2Type:App\\Domain\\Project\\ValueObject\\ProjectId)\'');
		$this->addSql('ALTER TABLE user_has_project ADD CONSTRAINT FK_E15971A0A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function down(Schema $schema): void
	{
		$this->addSql('DROP SEQUENCE user_has_project_id_seq CASCADE');
		$this->addSql('DROP TABLE project');
		$this->addSql('DROP TABLE user_has_project');
	}
}
