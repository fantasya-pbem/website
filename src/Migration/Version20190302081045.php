<?php
declare(strict_types = 1);
namespace App\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190302081045 extends AbstractMigration
{
	public function getDescription(): string {
		return 'Create table myth.';
	}

	public function up(Schema $schema): void {
		$this->addSql('CREATE TABLE myth (id INT AUTO_INCREMENT NOT NULL, myth VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
	}

	public function down(Schema $schema): void {
		$this->addSql('DROP TABLE myth');
	}
}
