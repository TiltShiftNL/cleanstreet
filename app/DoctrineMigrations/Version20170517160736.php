<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170517160736 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE contactpersoon_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE lediging_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE medewerker_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE onderneming_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE contactpersoon (id VARCHAR(255) NOT NULL, onderneming_id VARCHAR(255) NOT NULL, naam VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, telefoon VARCHAR(25) DEFAULT NULL, functie VARCHAR(255) DEFAULT NULL, actief BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B1E04A49D74F31B4 ON contactpersoon (onderneming_id)');
        $this->addSql('CREATE TABLE lediging (id VARCHAR(255) NOT NULL, ondernemers_bak_uuid VARCHAR(36) NOT NULL, medewerker_id INT NOT NULL, datum_tijd TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, opmerkingen TEXT DEFAULT NULL, vul_percentage INT NOT NULL, vervangen BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F5F0DC684FD32BFF ON lediging (ondernemers_bak_uuid)');
        $this->addSql('CREATE INDEX IDX_F5F0DC683D707F64 ON lediging (medewerker_id)');
        $this->addSql('CREATE TABLE medewerker (id INT NOT NULL, naam VARCHAR(255) NOT NULL, actief BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE ondernemers_bak (uuid VARCHAR(36) NOT NULL, onderneming_id VARCHAR(255) NOT NULL, kenmerk VARCHAR(4) NOT NULL, actief BOOLEAN NOT NULL, opmerkingen TEXT DEFAULT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX IDX_934FED8BD74F31B4 ON ondernemers_bak (onderneming_id)');
        $this->addSql('CREATE UNIQUE INDEX uq_kenmerk ON ondernemers_bak (kenmerk)');
        $this->addSql('CREATE TABLE onderneming (id VARCHAR(255) NOT NULL, naam VARCHAR(255) NOT NULL, straat VARCHAR(125) NOT NULL, nummer VARCHAR(25) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_straat ON onderneming (straat)');
        $this->addSql('ALTER TABLE contactpersoon ADD CONSTRAINT FK_B1E04A49D74F31B4 FOREIGN KEY (onderneming_id) REFERENCES onderneming (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lediging ADD CONSTRAINT FK_F5F0DC684FD32BFF FOREIGN KEY (ondernemers_bak_uuid) REFERENCES ondernemers_bak (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lediging ADD CONSTRAINT FK_F5F0DC683D707F64 FOREIGN KEY (medewerker_id) REFERENCES medewerker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ondernemers_bak ADD CONSTRAINT FK_934FED8BD74F31B4 FOREIGN KEY (onderneming_id) REFERENCES onderneming (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE lediging DROP CONSTRAINT FK_F5F0DC683D707F64');
        $this->addSql('ALTER TABLE lediging DROP CONSTRAINT FK_F5F0DC684FD32BFF');
        $this->addSql('ALTER TABLE contactpersoon DROP CONSTRAINT FK_B1E04A49D74F31B4');
        $this->addSql('ALTER TABLE ondernemers_bak DROP CONSTRAINT FK_934FED8BD74F31B4');
        $this->addSql('DROP SEQUENCE contactpersoon_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE lediging_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE medewerker_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE onderneming_id_seq CASCADE');
        $this->addSql('DROP TABLE contactpersoon');
        $this->addSql('DROP TABLE lediging');
        $this->addSql('DROP TABLE medewerker');
        $this->addSql('DROP TABLE ondernemers_bak');
        $this->addSql('DROP TABLE onderneming');
    }
}
