<?php
declare(strict_types = 1);
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221221161657 extends AbstractMigration
{
	public function getDescription(): string {
		return 'Add retired status to assignments.';
	}

	public function up(Schema $schema): void {
		$this->addSql('ALTER TABLE assignment ADD COLUMN retired tinyint(1) unsigned NOT NULL DEFAULT 0');
	}

	public function down(Schema $schema): void {
		$this->addSql('ALTER TABLE assignment DROP COLUMN retired');
	}
}
