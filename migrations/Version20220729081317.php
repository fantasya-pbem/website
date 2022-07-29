<?php
declare(strict_types = 1);
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220729081317 extends AbstractMigration
{
	public function getDescription(): string {
		return 'Add column can_enter to table game.';
	}

	public function up(Schema $schema): void {
		$this->addSql("ALTER TABLE game ADD `can_enter` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `is_active`");
		$this->addSql("UPDATE game SET `can_enter` = 1 WHERE `alias` = 'lemuria'");
	}

	public function down(Schema $schema): void {
		$this->addSql("ALTER TABLE game DROP `can_enter`");
	}
}
