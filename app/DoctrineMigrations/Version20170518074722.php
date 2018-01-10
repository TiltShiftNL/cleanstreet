<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170518074722 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE observatie_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE observatie (id VARCHAR(255) NOT NULL, onderneming_id VARCHAR(255) DEFAULT NULL, ondernemers_bak_uuid VARCHAR(36) DEFAULT NULL, lediging_id VARCHAR(255) DEFAULT NULL, datum_tijd TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, tekst TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_235FC7EED74F31B4 ON observatie (onderneming_id)');
        $this->addSql('CREATE INDEX IDX_235FC7EE4FD32BFF ON observatie (ondernemers_bak_uuid)');
        $this->addSql('CREATE INDEX IDX_235FC7EEB0B52752 ON observatie (lediging_id)');
        $this->addSql('ALTER TABLE observatie ADD CONSTRAINT FK_235FC7EED74F31B4 FOREIGN KEY (onderneming_id) REFERENCES onderneming (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE observatie ADD CONSTRAINT FK_235FC7EE4FD32BFF FOREIGN KEY (ondernemers_bak_uuid) REFERENCES ondernemers_bak (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE observatie ADD CONSTRAINT FK_235FC7EEB0B52752 FOREIGN KEY (lediging_id) REFERENCES lediging (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE observatie_id_seq CASCADE');
        $this->addSql('DROP TABLE observatie');
    }
}
