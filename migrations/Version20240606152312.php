<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240606152312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE books (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, title VARCHAR(255) NOT NULL, content TEXT, user_id INT DEFAULT NULL, INDEX IDX_4A1B2A92A76ED395 (user_id), PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE events (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, title VARCHAR(255) NOT NULL, content TEXT, user_id INT DEFAULT NULL, INDEX IDX_5387574AA76ED395 (user_id), PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE books ADD CONSTRAINT FK_4A1B2A92A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE articles CHANGE content content TEXT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE books DROP FOREIGN KEY FK_4A1B2A92A76ED395');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574AA76ED395');
        $this->addSql('DROP TABLE books');
        $this->addSql('DROP TABLE events');
        $this->addSql('ALTER TABLE articles CHANGE content content TEXT DEFAULT NULL');
    }
}
