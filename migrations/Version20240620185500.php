<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240620185500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medias DROP FOREIGN KEY FK_12D2AF81A76ED395');
        $this->addSql('DROP INDEX IDX_12D2AF81A76ED395 ON medias');
        $this->addSql('ALTER TABLE medias CHANGE user_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE medias ADD CONSTRAINT FK_12D2AF81F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_12D2AF81F675F31B ON medias (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medias DROP FOREIGN KEY FK_12D2AF81F675F31B');
        $this->addSql('DROP INDEX IDX_12D2AF81F675F31B ON medias');
        $this->addSql('ALTER TABLE medias CHANGE author_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE medias ADD CONSTRAINT FK_12D2AF81A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_12D2AF81A76ED395 ON medias (user_id)');
    }
}
