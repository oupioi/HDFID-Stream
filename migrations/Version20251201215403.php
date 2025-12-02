<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251201215403 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE IF EXISTS watch_history');
        $this->addSql('ALTER TABLE user ADD COLUMN last_login_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD COLUMN last_login_ip VARCHAR(45) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE watch_history (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, item_type VARCHAR(50) NOT NULL COLLATE "BINARY", item_id VARCHAR(50) NOT NULL COLLATE "BINARY", watched_at DATETIME NOT NULL, CONSTRAINT FK_watch_history_user FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_watch_history_type ON watch_history (item_type)');
        $this->addSql('CREATE INDEX IDX_watch_history_user ON watch_history (user_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, email, roles, password, first_name, last_name, username, created_at FROM "user"');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, username VARCHAR(50) DEFAULT NULL, created_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO "user" (id, email, roles, password, first_name, last_name, username, created_at) SELECT id, email, roles, password, first_name, last_name, username, created_at FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
    }
}
