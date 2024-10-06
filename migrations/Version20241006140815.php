<?php
declare(strict_types = 1);
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241006140815 extends AbstractMigration
{
	public function getDescription(): string {
		return 'Add creation date to myths.';
	}

	public function up(Schema $schema): void {
		$this->addSql('ALTER TABLE myth ADD COLUMN created_at DATE NOT NULL DEFAULT CURRENT_DATE AFTER id');
	}

	public function down(Schema $schema): void {
		$this->addSql('ALTER TABLE myth DROP COLUMN created_at');
	}
}
