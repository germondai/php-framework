<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240606141811 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles CHANGE title title VARCHAR(255) NOT NULL, CHANGE content content TEXT');
        $this->addSql('ALTER TABLE users CHANGE name name VARCHAR(255) NOT NULL, CHANGE surname surname VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles CHANGE title title VARCHAR(256) NOT NULL, CHANGE content content VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE name name VARCHAR(256) NOT NULL, CHANGE surname surname VARCHAR(256) NOT NULL, CHANGE email email VARCHAR(256) NOT NULL, CHANGE password password VARCHAR(256) NOT NULL');
    }
}
