<?php
declare(strict_types = 1);
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210416080617 extends AbstractMigration
{
	public function getDescription(): string {
		return 'Remove newbie data from table assignment.';
	}

	public function up(Schema $schema): void {
		$this->addSql('ALTER TABLE assignment DROP COLUMN newbie');
	}

	public function down(Schema $schema): void {
		$this->addSql('ALTER TABLE assignment ADD COLUMN newbie mediumtext DEFAULT NULL');
	}
}
