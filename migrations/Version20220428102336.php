<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220428102336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE goat_command (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, quantite INTEGER NOT NULL)');
        $this->addSql('DROP INDEX UNIQ_E4171AFC19EB6921');
        $this->addSql('CREATE TEMPORARY TABLE __temp__im22_Panier AS SELECT id, client_id, total_price FROM im22_Panier');
        $this->addSql('DROP TABLE im22_Panier');
        $this->addSql('CREATE TABLE im22_Panier (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_id INTEGER NOT NULL, total_price DOUBLE PRECISION NOT NULL, CONSTRAINT FK_E4171AFC19EB6921 FOREIGN KEY (client_id) REFERENCES im22_UserV2 (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO im22_Panier (id, client_id, total_price) SELECT id, client_id, total_price FROM __temp__im22_Panier');
        $this->addSql('DROP TABLE __temp__im22_Panier');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E4171AFC19EB6921 ON im22_Panier (client_id)');
        $this->addSql('DROP INDEX IDX_3067F108F3D0658E');
        $this->addSql('DROP INDEX IDX_3067F108F77D927C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__im22_PanierGoat AS SELECT id, panier_id, goat_id, quantite FROM im22_PanierGoat');
        $this->addSql('DROP TABLE im22_PanierGoat');
        $this->addSql('CREATE TABLE im22_PanierGoat (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, panier_id INTEGER NOT NULL, goat_id INTEGER NOT NULL, quantite INTEGER NOT NULL, CONSTRAINT FK_3067F108F77D927C FOREIGN KEY (panier_id) REFERENCES im22_Panier (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_3067F108F3D0658E FOREIGN KEY (goat_id) REFERENCES im22_Goat (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO im22_PanierGoat (id, panier_id, goat_id, quantite) SELECT id, panier_id, goat_id, quantite FROM __temp__im22_PanierGoat');
        $this->addSql('DROP TABLE __temp__im22_PanierGoat');
        $this->addSql('CREATE INDEX IDX_3067F108F3D0658E ON im22_PanierGoat (goat_id)');
        $this->addSql('CREATE INDEX IDX_3067F108F77D927C ON im22_PanierGoat (panier_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE goat_command');
        $this->addSql('DROP INDEX UNIQ_E4171AFC19EB6921');
        $this->addSql('CREATE TEMPORARY TABLE __temp__im22_Panier AS SELECT id, client_id, total_price FROM im22_Panier');
        $this->addSql('DROP TABLE im22_Panier');
        $this->addSql('CREATE TABLE im22_Panier (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_id INTEGER NOT NULL, total_price DOUBLE PRECISION NOT NULL)');
        $this->addSql('INSERT INTO im22_Panier (id, client_id, total_price) SELECT id, client_id, total_price FROM __temp__im22_Panier');
        $this->addSql('DROP TABLE __temp__im22_Panier');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E4171AFC19EB6921 ON im22_Panier (client_id)');
        $this->addSql('DROP INDEX IDX_3067F108F77D927C');
        $this->addSql('DROP INDEX IDX_3067F108F3D0658E');
        $this->addSql('CREATE TEMPORARY TABLE __temp__im22_PanierGoat AS SELECT id, panier_id, goat_id, quantite FROM im22_PanierGoat');
        $this->addSql('DROP TABLE im22_PanierGoat');
        $this->addSql('CREATE TABLE im22_PanierGoat (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, panier_id INTEGER NOT NULL, goat_id INTEGER NOT NULL, quantite INTEGER NOT NULL)');
        $this->addSql('INSERT INTO im22_PanierGoat (id, panier_id, goat_id, quantite) SELECT id, panier_id, goat_id, quantite FROM __temp__im22_PanierGoat');
        $this->addSql('DROP TABLE __temp__im22_PanierGoat');
        $this->addSql('CREATE INDEX IDX_3067F108F77D927C ON im22_PanierGoat (panier_id)');
        $this->addSql('CREATE INDEX IDX_3067F108F3D0658E ON im22_PanierGoat (goat_id)');
    }
}
