<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170609081114 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE notitie_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE notitie (id VARCHAR(255) NOT NULL, medewerker_id INT DEFAULT NULL, onderneming_id VARCHAR(255) DEFAULT NULL, datum_tijd TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, tekst TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2F242E7A3D707F64 ON notitie (medewerker_id)');
        $this->addSql('CREATE INDEX IDX_2F242E7AD74F31B4 ON notitie (onderneming_id)');
        $this->addSql('CREATE TABLE notitie_fotos (observatie_id VARCHAR(255) NOT NULL, foto_id VARCHAR(255) NOT NULL, PRIMARY KEY(observatie_id, foto_id))');
        $this->addSql('CREATE INDEX IDX_89B56F2DBA523555 ON notitie_fotos (observatie_id)');
        $this->addSql('CREATE INDEX IDX_89B56F2D7ABFA656 ON notitie_fotos (foto_id)');
        $this->addSql('ALTER TABLE notitie ADD CONSTRAINT FK_2F242E7A3D707F64 FOREIGN KEY (medewerker_id) REFERENCES medewerker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notitie ADD CONSTRAINT FK_2F242E7AD74F31B4 FOREIGN KEY (onderneming_id) REFERENCES onderneming (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notitie_fotos ADD CONSTRAINT FK_89B56F2DBA523555 FOREIGN KEY (observatie_id) REFERENCES notitie (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notitie_fotos ADD CONSTRAINT FK_89B56F2D7ABFA656 FOREIGN KEY (foto_id) REFERENCES foto (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE notitie_fotos DROP CONSTRAINT FK_89B56F2DBA523555');
        $this->addSql('DROP SEQUENCE notitie_id_seq CASCADE');
        $this->addSql('DROP TABLE notitie');
        $this->addSql('DROP TABLE notitie_fotos');
    }
}
