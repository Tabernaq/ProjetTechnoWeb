<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220426160145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_E4171AFC19EB6921');
        $this->addSql('CREATE TEMPORARY TABLE __temp__im22_Panier AS SELECT id, client_id, total_price FROM im22_Panier');
        $this->addSql('DROP TABLE im22_Panier');
        $this->addSql('CREATE TABLE im22_Panier (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_id INTEGER NOT NULL, total_price INTEGER NOT NULL, CONSTRAINT FK_E4171AFC19EB6921 FOREIGN KEY (client_id) REFERENCES im22_UserV2 (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO im22_Panier (id, client_id, total_price) SELECT id, client_id, total_price FROM __temp__im22_Panier');
        $this->addSql('DROP TABLE __temp__im22_Panier');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E4171AFC19EB6921 ON im22_Panier (client_id)');
        $this->addSql('DROP INDEX IDX_1B565601F77D927C');
        $this->addSql('DROP INDEX IDX_1B565601F3D0658E');
        $this->addSql('CREATE TEMPORARY TABLE __temp__panier_goat AS SELECT panier_id, goat_id FROM panier_goat');
        $this->addSql('DROP TABLE panier_goat');
        $this->addSql('CREATE TABLE panier_goat (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, panier_id INTEGER NOT NULL, goat_id INTEGER NOT NULL, quantite INTEGER NOT NULL, CONSTRAINT FK_1B565601F77D927C FOREIGN KEY (panier_id) REFERENCES im22_Panier (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_1B565601F3D0658E FOREIGN KEY (goat_id) REFERENCES im22_Goat (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO panier_goat (panier_id, goat_id) SELECT panier_id, goat_id FROM __temp__panier_goat');
        $this->addSql('DROP TABLE __temp__panier_goat');
        $this->addSql('CREATE INDEX IDX_1B565601F77D927C ON panier_goat (panier_id)');
        $this->addSql('CREATE INDEX IDX_1B565601F3D0658E ON panier_goat (goat_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_E4171AFC19EB6921');
        $this->addSql('CREATE TEMPORARY TABLE __temp__im22_Panier AS SELECT id, client_id, total_price FROM im22_Panier');
        $this->addSql('DROP TABLE im22_Panier');
        $this->addSql('CREATE TABLE im22_Panier (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_id INTEGER NOT NULL, total_price INTEGER NOT NULL)');
        $this->addSql('INSERT INTO im22_Panier (id, client_id, total_price) SELECT id, client_id, total_price FROM __temp__im22_Panier');
        $this->addSql('DROP TABLE __temp__im22_Panier');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E4171AFC19EB6921 ON im22_Panier (client_id)');
        $this->addSql('DROP INDEX IDX_1B565601F77D927C');
        $this->addSql('DROP INDEX IDX_1B565601F3D0658E');
        $this->addSql('CREATE TEMPORARY TABLE __temp__panier_goat AS SELECT panier_id, goat_id FROM panier_goat');
        $this->addSql('DROP TABLE panier_goat');
        $this->addSql('CREATE TABLE panier_goat (panier_id INTEGER NOT NULL, goat_id INTEGER NOT NULL, PRIMARY KEY(panier_id, goat_id))');
        $this->addSql('INSERT INTO panier_goat (panier_id, goat_id) SELECT panier_id, goat_id FROM __temp__panier_goat');
        $this->addSql('DROP TABLE __temp__panier_goat');
        $this->addSql('CREATE INDEX IDX_1B565601F77D927C ON panier_goat (panier_id)');
        $this->addSql('CREATE INDEX IDX_1B565601F3D0658E ON panier_goat (goat_id)');
    }
}
