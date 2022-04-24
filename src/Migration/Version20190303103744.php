<?php
declare(strict_types = 1);
namespace App\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190303103744 extends AbstractMigration
{
	public function getDescription(): string {
		return 'Create table game.';
	}

	public function up(Schema $schema): void {
		$this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(190) NOT NULL, description TEXT NOT NULL, db VARCHAR(32) NOT NULL, alias VARCHAR(32) NOT NULL, is_active TINYINT(1) NOT NULL, start_day SMALLINT NOT NULL, start_hour SMALLINT NOT NULL, UNIQUE INDEX UQ_game_name (name), UNIQUE INDEX UQ_game_db (db), UNIQUE INDEX UQ_game_alias (alias), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
	}

	public function down(Schema $schema): void {
		$this->addSql('DROP TABLE game');
	}
}
