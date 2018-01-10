<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170706130743 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE crow_meting_fotos RENAME TO legacy_crow_meting_fotos');
        $this->addSql('ALTER TABLE crow_meting RENAME TO legacy_crow_meting');
        $this->addSql('ALTER TABLE observatie_fotos RENAME TO legacy_observatie_fotos');
        $this->addSql('ALTER TABLE observatie RENAME TO legacy_observatie');
        $this->addSql('ALTER TABLE lediging_fotos RENAME TO legacy_lediging_fotos');
        $this->addSql('ALTER TABLE lediging RENAME TO legacy_lediging');
        $this->addSql('ALTER TABLE contactpersoon RENAME TO legacy_contactpersoon');
        $this->addSql('ALTER TABLE melding RENAME TO legacy_melding');
        $this->addSql('DROP INDEX idx_89b56f2d823c564');
        $this->addSql('DROP INDEX idx_89b56f2d7abfa656');
        $this->addSql('ALTER TABLE notitie_fotos RENAME TO legacy_notitie_fotos');
        $this->addSql('ALTER TABLE notitie RENAME TO legacy_notitie');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

    }
}
