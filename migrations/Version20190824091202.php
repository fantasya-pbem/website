<?php
declare(strict_types = 1);
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190824091202 extends AbstractMigration
{
	public function getDescription(): string {
		return 'Add column flags to table user.';
	}

	public function up(Schema $schema): void {
		$this->addSql('ALTER TABLE user ADD flags SMALLINT NOT NULL DEFAULT 0 AFTER roles');
	}

	public function down(Schema $schema): void {
		$this->addSql('ALTER TABLE user DROP flags');
	}
}
