<?php
declare(strict_types = 1);
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210302181403 extends AbstractMigration
{
	public function getDescription(): string {
		return 'Add column engine to table game.';
	}

	public function up(Schema $schema): void {
		$this->addSql("ALTER TABLE game ADD `engine` char(8) NOT NULL DEFAULT 'fantasya' AFTER description");
	}

	public function down(Schema $schema): void {
		$this->addSql("ALTER TABLE game DROP `engine`");
	}
}
