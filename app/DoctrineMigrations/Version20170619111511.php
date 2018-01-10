<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170619111511 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE notitie ADD hoofd_notitie_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE notitie ADD CONSTRAINT FK_2F242E7AEFBB3238 FOREIGN KEY (hoofd_notitie_id) REFERENCES notitie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2F242E7AEFBB3238 ON notitie (hoofd_notitie_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE notitie DROP CONSTRAINT FK_2F242E7AEFBB3238');
        $this->addSql('DROP INDEX IDX_2F242E7AEFBB3238');
        $this->addSql('ALTER TABLE notitie DROP hoofd_notitie_id');
    }
}
