<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220423120644 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_EB3FA09DAA08CB10');
        $this->addSql('CREATE TEMPORARY TABLE __temp__im22_UserV2 AS SELECT id, login, roles, password, name, surname, date_birth FROM im22_UserV2');
        $this->addSql('DROP TABLE im22_UserV2');
        $this->addSql('CREATE TABLE im22_UserV2 (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, login VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, date_birth DATE NOT NULL)');
        $this->addSql('INSERT INTO im22_UserV2 (id, login, roles, password, name, surname, date_birth) SELECT id, login, roles, password, name, surname, date_birth FROM __temp__im22_UserV2');
        $this->addSql('DROP TABLE __temp__im22_UserV2');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EB3FA09DAA08CB10 ON im22_UserV2 (login)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE im22_UserV2 ADD COLUMN status VARCHAR(255) NOT NULL');
    }
}
