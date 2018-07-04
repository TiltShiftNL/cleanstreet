<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180517150035 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE categorie ADD pictogram_naam VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE categorie SET pictogram_naam = \'drie-vuilniszakken.svg\' WHERE subcategorie = \'3 tot 7 zakken\'');
        $this->addSql('UPDATE categorie SET pictogram_naam = \'zeven-vuilniszakken.svg\' WHERE subcategorie = \'7 of meer zakken\'');
        $this->addSql('UPDATE categorie SET pictogram_naam = \'glas.svg\' WHERE subcategorie = \'Glas\'');
        $this->addSql('UPDATE categorie SET pictogram_naam = \'karton.svg\' WHERE subcategorie = \'Karton\'');
        $this->addSql('UPDATE categorie SET pictogram_naam = \'huisraad.svg\' WHERE subcategorie = \'Huisraad\'');
        $this->addSql('UPDATE categorie SET pictogram_naam = \'overig.svg\' WHERE subcategorie = \'Overig\'');
        $this->addSql('UPDATE categorie SET pictogram_naam = \'puin.svg\' WHERE subcategorie = \'Puin\'');
        $this->addSql('UPDATE categorie SET pictogram_naam = \'horeca.svg\' WHERE subcategorie = \'Horeca\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE categorie DROP pictogram_naam');
    }
}
