<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171117162854 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE telefoonboek_entry ADD gebied_id INT NULL');
        $this->addSql('UPDATE telefoonboek_entry SET gebied_id = (SELECT id FROM gebied ORDER BY id ASC LIMIT 1)');
        $this->addSql('ALTER TABLE telefoonboek_entry ALTER gebied_id SET NOT NULL');
        $this->addSql('ALTER TABLE telefoonboek_entry ADD CONSTRAINT FK_9F1BEBA2944D109 FOREIGN KEY (gebied_id) REFERENCES gebied (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9F1BEBA2944D109 ON telefoonboek_entry (gebied_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE telefoonboek_entry DROP CONSTRAINT FK_9F1BEBA2944D109');
        $this->addSql('DROP INDEX IDX_9F1BEBA2944D109');
        $this->addSql('ALTER TABLE telefoonboek_entry DROP gebied_id');
    }
}
