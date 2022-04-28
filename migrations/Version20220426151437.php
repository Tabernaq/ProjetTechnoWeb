<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220426151437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE panier (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_id INTEGER NOT NULL, total_price INTEGER NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_24CC0DF219EB6921 ON panier (client_id)');
        $this->addSql('CREATE TABLE panier_goat (panier_id INTEGER NOT NULL, goat_id INTEGER NOT NULL, PRIMARY KEY(panier_id, goat_id))');
        $this->addSql('CREATE INDEX IDX_1B565601F77D927C ON panier_goat (panier_id)');
        $this->addSql('CREATE INDEX IDX_1B565601F3D0658E ON panier_goat (goat_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE panier_goat');
    }
}
