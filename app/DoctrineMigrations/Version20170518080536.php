<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170518080536 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE lediging ADD geo_point geography(POINT, 4326) DEFAULT NULL');
        $this->addSql('ALTER TABLE observatie ADD geo_point geography(POINT, 4326) DEFAULT NULL');
        $this->addSql('ALTER TABLE onderneming ADD geo_point geography(POINT, 4326) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE onderneming DROP geo_point');
        $this->addSql('ALTER TABLE lediging DROP geo_point');
        $this->addSql('ALTER TABLE observatie DROP geo_point');
    }
}
