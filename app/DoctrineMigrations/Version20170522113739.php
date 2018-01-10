<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170522113739 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE contactpersoon DROP CONSTRAINT FK_B1E04A49D74F31B4');
        $this->addSql('ALTER TABLE contactpersoon ADD CONSTRAINT FK_B1E04A49D74F31B4 FOREIGN KEY (onderneming_id) REFERENCES onderneming (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lediging_fotos DROP CONSTRAINT FK_DC7751A8B0B52752');
        $this->addSql('ALTER TABLE lediging_fotos DROP CONSTRAINT FK_DC7751A87ABFA656');
        $this->addSql('ALTER TABLE lediging_fotos ADD CONSTRAINT FK_DC7751A8B0B52752 FOREIGN KEY (lediging_id) REFERENCES lediging (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lediging_fotos ADD CONSTRAINT FK_DC7751A87ABFA656 FOREIGN KEY (foto_id) REFERENCES foto (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE observatie_fotos DROP CONSTRAINT FK_9FA2402FBA523555');
        $this->addSql('ALTER TABLE observatie_fotos DROP CONSTRAINT FK_9FA2402F7ABFA656');
        $this->addSql('ALTER TABLE observatie_fotos ADD CONSTRAINT FK_9FA2402FBA523555 FOREIGN KEY (observatie_id) REFERENCES observatie (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE observatie_fotos ADD CONSTRAINT FK_9FA2402F7ABFA656 FOREIGN KEY (foto_id) REFERENCES foto (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ondernemers_bak_fotos DROP CONSTRAINT FK_D9AD40EC4FD32BFF');
        $this->addSql('ALTER TABLE ondernemers_bak_fotos DROP CONSTRAINT FK_D9AD40EC7ABFA656');
        $this->addSql('ALTER TABLE ondernemers_bak_fotos ADD CONSTRAINT FK_D9AD40EC4FD32BFF FOREIGN KEY (ondernemers_bak_uuid) REFERENCES ondernemers_bak (uuid) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ondernemers_bak_fotos ADD CONSTRAINT FK_D9AD40EC7ABFA656 FOREIGN KEY (foto_id) REFERENCES foto (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE contactpersoon DROP CONSTRAINT fk_b1e04a49d74f31b4');
        $this->addSql('ALTER TABLE contactpersoon ADD CONSTRAINT fk_b1e04a49d74f31b4 FOREIGN KEY (onderneming_id) REFERENCES onderneming (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lediging_fotos DROP CONSTRAINT fk_dc7751a8b0b52752');
        $this->addSql('ALTER TABLE lediging_fotos DROP CONSTRAINT fk_dc7751a87abfa656');
        $this->addSql('ALTER TABLE lediging_fotos ADD CONSTRAINT fk_dc7751a8b0b52752 FOREIGN KEY (lediging_id) REFERENCES lediging (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lediging_fotos ADD CONSTRAINT fk_dc7751a87abfa656 FOREIGN KEY (foto_id) REFERENCES foto (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE observatie_fotos DROP CONSTRAINT fk_9fa2402fba523555');
        $this->addSql('ALTER TABLE observatie_fotos DROP CONSTRAINT fk_9fa2402f7abfa656');
        $this->addSql('ALTER TABLE observatie_fotos ADD CONSTRAINT fk_9fa2402fba523555 FOREIGN KEY (observatie_id) REFERENCES observatie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE observatie_fotos ADD CONSTRAINT fk_9fa2402f7abfa656 FOREIGN KEY (foto_id) REFERENCES foto (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ondernemers_bak_fotos DROP CONSTRAINT fk_d9ad40ec4fd32bff');
        $this->addSql('ALTER TABLE ondernemers_bak_fotos DROP CONSTRAINT fk_d9ad40ec7abfa656');
        $this->addSql('ALTER TABLE ondernemers_bak_fotos ADD CONSTRAINT fk_d9ad40ec4fd32bff FOREIGN KEY (ondernemers_bak_uuid) REFERENCES ondernemers_bak (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ondernemers_bak_fotos ADD CONSTRAINT fk_d9ad40ec7abfa656 FOREIGN KEY (foto_id) REFERENCES foto (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
