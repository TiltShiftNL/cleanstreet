<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170706133044 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE contactpersoon_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE lediging_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE observatie_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE melding_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE crow_meting_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE notitie_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE actie_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ticket_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE actie (id VARCHAR(255) NOT NULL, medewerker_id INT DEFAULT NULL, ticket_id VARCHAR(255) NOT NULL, telefoonboekentry_id INT DEFAULT NULL, datum_tijd_aangemaakt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type VARCHAR(255) NOT NULL, tekst TEXT DEFAULT NULL, nummer VARCHAR(255) DEFAULT NULL, oude_status BOOLEAN DEFAULT NULL, oude_oplossing VARCHAR(255) DEFAULT NULL, nieuwe_status BOOLEAN DEFAULT NULL, nieuwe_oplossing VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_91FC74B83D707F64 ON actie (medewerker_id)');
        $this->addSql('CREATE INDEX IDX_91FC74B8700047D2 ON actie (ticket_id)');
        $this->addSql('CREATE INDEX IDX_91FC74B8C4166F91 ON actie (telefoonboekentry_id)');
        $this->addSql('CREATE TABLE actie_fotos (actie_id VARCHAR(255) NOT NULL, foto_id VARCHAR(255) NOT NULL, PRIMARY KEY(actie_id, foto_id))');
        $this->addSql('CREATE INDEX IDX_8B164594525468CC ON actie_fotos (actie_id)');
        $this->addSql('CREATE INDEX IDX_8B1645947ABFA656 ON actie_fotos (foto_id)');
        $this->addSql('CREATE TABLE ticket (id VARCHAR(255) NOT NULL, medewerker_id INT DEFAULT NULL, onderneming_id VARCHAR(255) DEFAULT NULL, ondernemers_bak_uuid VARCHAR(36) NOT NULL, bron VARCHAR(50) NOT NULL, datum_tijd_aangemaakt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, datum_tijd_gewijzigd TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, datum_tijd_gesloten TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, status BOOLEAN NOT NULL, oplossing VARCHAR(255) DEFAULT NULL, geo geography(POINT, 4326) DEFAULT NULL, straat VARCHAR(255) DEFAULT NULL, nummer VARCHAR(50) DEFAULT NULL, verwijderd BOOLEAN NOT NULL, type VARCHAR(255) NOT NULL, tekst TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_97A0ADA33D707F64 ON ticket (medewerker_id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA3D74F31B4 ON ticket (onderneming_id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA34FD32BFF ON ticket (ondernemers_bak_uuid)');
        $this->addSql('COMMENT ON COLUMN ticket.geo IS \'(DC2Type:geography)\'');
        $this->addSql('CREATE TABLE notitie_fotos (notitie_id VARCHAR(255) NOT NULL, foto_id VARCHAR(255) NOT NULL, PRIMARY KEY(notitie_id, foto_id))');
        $this->addSql('CREATE INDEX IDX_89B56F2D823C564 ON notitie_fotos (notitie_id)');
        $this->addSql('CREATE INDEX IDX_89B56F2D7ABFA656 ON notitie_fotos (foto_id)');
        $this->addSql('ALTER TABLE actie ADD CONSTRAINT FK_91FC74B83D707F64 FOREIGN KEY (medewerker_id) REFERENCES medewerker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE actie ADD CONSTRAINT FK_91FC74B8700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE actie ADD CONSTRAINT FK_91FC74B8C4166F91 FOREIGN KEY (telefoonboekentry_id) REFERENCES telefoonboek_entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE actie_fotos ADD CONSTRAINT FK_8B164594525468CC FOREIGN KEY (actie_id) REFERENCES actie (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE actie_fotos ADD CONSTRAINT FK_8B1645947ABFA656 FOREIGN KEY (foto_id) REFERENCES foto (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA33D707F64 FOREIGN KEY (medewerker_id) REFERENCES medewerker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3D74F31B4 FOREIGN KEY (onderneming_id) REFERENCES onderneming (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA34FD32BFF FOREIGN KEY (ondernemers_bak_uuid) REFERENCES ondernemers_bak (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notitie_fotos ADD CONSTRAINT FK_89B56F2D823C564 FOREIGN KEY (notitie_id) REFERENCES ticket (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notitie_fotos ADD CONSTRAINT FK_89B56F2D7ABFA656 FOREIGN KEY (foto_id) REFERENCES foto (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ondernemers_bak DROP opmerkingen');
        $this->addSql('ALTER TABLE onderneming DROP opmerkingen');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE actie_fotos DROP CONSTRAINT FK_8B164594525468CC');
        $this->addSql('ALTER TABLE actie DROP CONSTRAINT FK_91FC74B8700047D2');
        $this->addSql('ALTER TABLE notitie_fotos DROP CONSTRAINT FK_89B56F2D823C564');
        $this->addSql('DROP SEQUENCE actie_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ticket_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE contactpersoon_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE lediging_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE observatie_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE melding_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE crow_meting_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE notitie_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP TABLE actie');
        $this->addSql('DROP TABLE actie_fotos');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE notitie_fotos');
        $this->addSql('ALTER TABLE ondernemers_bak ADD opmerkingen TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE onderneming ADD opmerkingen TEXT DEFAULT NULL');
    }
}
