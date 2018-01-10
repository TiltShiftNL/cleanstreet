<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170518081654 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE foto_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE foto (id VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, datum_tijd_upload TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, datum_tijd_exif TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, geo_upload geography(POINT, 4326) DEFAULT NULL, geo_exif geography(POINT, 4326) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE lediging_fotos (lediging_id VARCHAR(255) NOT NULL, foto_id VARCHAR(255) NOT NULL, PRIMARY KEY(lediging_id, foto_id))');
        $this->addSql('CREATE INDEX IDX_DC7751A8B0B52752 ON lediging_fotos (lediging_id)');
        $this->addSql('CREATE INDEX IDX_DC7751A87ABFA656 ON lediging_fotos (foto_id)');
        $this->addSql('CREATE TABLE observatie_fotos (observatie_id VARCHAR(255) NOT NULL, foto_id VARCHAR(255) NOT NULL, PRIMARY KEY(observatie_id, foto_id))');
        $this->addSql('CREATE INDEX IDX_9FA2402FBA523555 ON observatie_fotos (observatie_id)');
        $this->addSql('CREATE INDEX IDX_9FA2402F7ABFA656 ON observatie_fotos (foto_id)');
        $this->addSql('CREATE TABLE ondernemers_bak_fotos (ondernemers_bak_uuid VARCHAR(36) NOT NULL, foto_id VARCHAR(255) NOT NULL, PRIMARY KEY(ondernemers_bak_uuid, foto_id))');
        $this->addSql('CREATE INDEX IDX_D9AD40EC4FD32BFF ON ondernemers_bak_fotos (ondernemers_bak_uuid)');
        $this->addSql('CREATE INDEX IDX_D9AD40EC7ABFA656 ON ondernemers_bak_fotos (foto_id)');
        $this->addSql('ALTER TABLE lediging_fotos ADD CONSTRAINT FK_DC7751A8B0B52752 FOREIGN KEY (lediging_id) REFERENCES lediging (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lediging_fotos ADD CONSTRAINT FK_DC7751A87ABFA656 FOREIGN KEY (foto_id) REFERENCES foto (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE observatie_fotos ADD CONSTRAINT FK_9FA2402FBA523555 FOREIGN KEY (observatie_id) REFERENCES observatie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE observatie_fotos ADD CONSTRAINT FK_9FA2402F7ABFA656 FOREIGN KEY (foto_id) REFERENCES foto (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ondernemers_bak_fotos ADD CONSTRAINT FK_D9AD40EC4FD32BFF FOREIGN KEY (ondernemers_bak_uuid) REFERENCES ondernemers_bak (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ondernemers_bak_fotos ADD CONSTRAINT FK_D9AD40EC7ABFA656 FOREIGN KEY (foto_id) REFERENCES foto (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE lediging_fotos DROP CONSTRAINT FK_DC7751A87ABFA656');
        $this->addSql('ALTER TABLE observatie_fotos DROP CONSTRAINT FK_9FA2402F7ABFA656');
        $this->addSql('ALTER TABLE ondernemers_bak_fotos DROP CONSTRAINT FK_D9AD40EC7ABFA656');
        $this->addSql('DROP SEQUENCE foto_id_seq CASCADE');
        $this->addSql('DROP TABLE foto');
        $this->addSql('DROP TABLE lediging_fotos');
        $this->addSql('DROP TABLE observatie_fotos');
        $this->addSql('DROP TABLE ondernemers_bak_fotos');
    }
}
