<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180517104135 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE categorie_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE categorie (id INT NOT NULL, hoofdcategorie VARCHAR(75) NOT NULL, subcategorie VARCHAR(75) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE ticket_categorie (ticket_id VARCHAR(255) NOT NULL, categorie_id INT NOT NULL, PRIMARY KEY(ticket_id, categorie_id))');
        $this->addSql('CREATE INDEX IDX_D3470C7E700047D2 ON ticket_categorie (ticket_id)');
        $this->addSql('CREATE INDEX IDX_D3470C7EBCF5E72D ON ticket_categorie (categorie_id)');
        $this->addSql('ALTER TABLE ticket_categorie ADD CONSTRAINT FK_D3470C7E700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket_categorie ADD CONSTRAINT FK_D3470C7EBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('INSERT INTO categorie (id, hoofdcategorie, subcategorie) VALUES (nextval(\'categorie_id_seq\'), \'Klein/Zwerf-vuil\', \'Klein/Zwerf-vuil\')');
        $this->addSql('INSERT INTO categorie (id, hoofdcategorie, subcategorie) VALUES (nextval(\'categorie_id_seq\'), \'Zakken\', \'3 tot 7 zakken\')');
        $this->addSql('INSERT INTO categorie (id, hoofdcategorie, subcategorie) VALUES (nextval(\'categorie_id_seq\'), \'Zakken\', \'7 of meer zakken\')');
        $this->addSql('INSERT INTO categorie (id, hoofdcategorie, subcategorie) VALUES (nextval(\'categorie_id_seq\'), \'Grofvuil\', \'Glas\')');
        $this->addSql('INSERT INTO categorie (id, hoofdcategorie, subcategorie) VALUES (nextval(\'categorie_id_seq\'), \'Grofvuil\', \'Karton\')');
        $this->addSql('INSERT INTO categorie (id, hoofdcategorie, subcategorie) VALUES (nextval(\'categorie_id_seq\'), \'Grofvuil\', \'Huisraad\')');
        $this->addSql('INSERT INTO categorie (id, hoofdcategorie, subcategorie) VALUES (nextval(\'categorie_id_seq\'), \'Grofvuil\', \'Puin\')');
        $this->addSql('INSERT INTO categorie (id, hoofdcategorie, subcategorie) VALUES (nextval(\'categorie_id_seq\'), \'Grofvuil\', \'Horeca\')');
        $this->addSql('INSERT INTO categorie (id, hoofdcategorie, subcategorie) VALUES (nextval(\'categorie_id_seq\'), \'Grofvuil\', \'Overig\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ticket_categorie DROP CONSTRAINT FK_D3470C7EBCF5E72D');
        $this->addSql('DROP SEQUENCE categorie_id_seq CASCADE');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE ticket_categorie');
    }
}
