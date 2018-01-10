<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170615114118 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE notitie_fotos DROP CONSTRAINT fk_89b56f2dba523555');
        $this->addSql('DROP INDEX idx_89b56f2dba523555');
        $this->addSql('ALTER TABLE notitie_fotos DROP CONSTRAINT notitie_fotos_pkey');
        $this->addSql('ALTER TABLE notitie_fotos RENAME COLUMN observatie_id TO notitie_id');
        $this->addSql('ALTER TABLE notitie_fotos ADD CONSTRAINT FK_89B56F2D823C564 FOREIGN KEY (notitie_id) REFERENCES notitie (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_89B56F2D823C564 ON notitie_fotos (notitie_id)');
        $this->addSql('ALTER TABLE notitie_fotos ADD PRIMARY KEY (notitie_id, foto_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE notitie_fotos DROP CONSTRAINT FK_89B56F2D823C564');
        $this->addSql('DROP INDEX IDX_89B56F2D823C564');
        $this->addSql('DROP INDEX notitie_fotos_pkey');
        $this->addSql('ALTER TABLE notitie_fotos RENAME COLUMN notitie_id TO observatie_id');
        $this->addSql('ALTER TABLE notitie_fotos ADD CONSTRAINT fk_89b56f2dba523555 FOREIGN KEY (observatie_id) REFERENCES notitie (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_89b56f2dba523555 ON notitie_fotos (observatie_id)');
        $this->addSql('ALTER TABLE notitie_fotos ADD PRIMARY KEY (observatie_id, foto_id)');
    }
}
