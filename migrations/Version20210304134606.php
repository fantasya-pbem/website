<?php
declare(strict_types = 1);
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210304134606 extends AbstractMigration
{
	public function getDescription(): string {
		return 'Create table assignment.';
	}

	public function up(Schema $schema): void {
		$this->addSql('CREATE TABLE assignment (uuid CHAR(36) NOT NULL PRIMARY KEY, user_id INT NOT NULL, newbie mediumtext DEFAULT NULL) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
		$this->addSql('ALTER TABLE assignment ADD CONSTRAINT FK_assignment_user FOREIGN KEY (user_id) REFERENCES user (id)');
	}

	public function down(Schema $schema): void {
		$this->addSql('DROP TABLE assignment');
	}
}
