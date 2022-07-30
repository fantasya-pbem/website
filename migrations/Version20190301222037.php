<?php
declare(strict_types = 1);
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190301222037 extends AbstractMigration
{
	public function getDescription(): string {
		return 'Create table news.';
	}

	public function up(Schema $schema): void {
		$this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, created_at DATE NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, PRIMARY KEY(id), UNIQUE UQ_news_created_at (created_at)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
	}

	public function down(Schema $schema): void {
		$this->addSql('DROP TABLE news');
	}
}
