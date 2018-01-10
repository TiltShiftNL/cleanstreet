<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170531065510 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE crow_meting_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE crow_meting (id VARCHAR(255) NOT NULL, onderneming_id VARCHAR(255) DEFAULT NULL, medewerker_opname_id INT NOT NULL, medewerker_beoordeling_id INT NOT NULL, datum_tijd_opname TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, datum_tijd_beoordeling TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_beoordeeld BOOLEAN NOT NULL, beoordeling INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CE94CADDD74F31B4 ON crow_meting (onderneming_id)');
        $this->addSql('CREATE INDEX IDX_CE94CADD84E2CDDC ON crow_meting (medewerker_opname_id)');
        $this->addSql('CREATE INDEX IDX_CE94CADDE0EAFBA ON crow_meting (medewerker_beoordeling_id)');
        $this->addSql('CREATE TABLE crow_meting_fotos (observatie_id VARCHAR(255) NOT NULL, foto_id VARCHAR(255) NOT NULL, PRIMARY KEY(observatie_id, foto_id))');
        $this->addSql('CREATE INDEX IDX_E5AADB69BA523555 ON crow_meting_fotos (observatie_id)');
        $this->addSql('CREATE INDEX IDX_E5AADB697ABFA656 ON crow_meting_fotos (foto_id)');
        $this->addSql('ALTER TABLE crow_meting ADD CONSTRAINT FK_CE94CADDD74F31B4 FOREIGN KEY (onderneming_id) REFERENCES onderneming (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE crow_meting ADD CONSTRAINT FK_CE94CADD84E2CDDC FOREIGN KEY (medewerker_opname_id) REFERENCES medewerker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE crow_meting ADD CONSTRAINT FK_CE94CADDE0EAFBA FOREIGN KEY (medewerker_beoordeling_id) REFERENCES medewerker (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE crow_meting_fotos ADD CONSTRAINT FK_E5AADB69BA523555 FOREIGN KEY (observatie_id) REFERENCES crow_meting (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE crow_meting_fotos ADD CONSTRAINT FK_E5AADB697ABFA656 FOREIGN KEY (foto_id) REFERENCES foto (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE crow_meting_fotos DROP CONSTRAINT FK_E5AADB69BA523555');
        $this->addSql('DROP SEQUENCE crow_meting_id_seq CASCADE');
        $this->addSql('DROP TABLE crow_meting');
        $this->addSql('DROP TABLE crow_meting_fotos');
    }
}
