<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240620114141 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE medias (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, alt VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, credit VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, extension VARCHAR(255) NOT NULL, size INT NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_12D2AF81A76ED395 (user_id), PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE medias ADD CONSTRAINT FK_12D2AF81A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medias DROP FOREIGN KEY FK_12D2AF81A76ED395');
        $this->addSql('DROP TABLE medias');
    }
}
