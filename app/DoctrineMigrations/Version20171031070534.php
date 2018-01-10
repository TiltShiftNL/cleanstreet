<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171031070534 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('INSERT INTO gebied (id, naam, telefoon) VALUES (nextval(\'gebied_id_seq\'), \'Damstraat, Damrak e.o.\', \'+31683807130\')');

        $this->addSql('ALTER TABLE dienst ADD gebied_id INT NULL');
        $this->addSql('UPDATE dienst SET gebied_id = (SELECT id FROM gebied ORDER BY id ASC LIMIT 1)');
        $this->addSql('ALTER TABLE dienst ADD CONSTRAINT FK_AB4E87D8944D109 FOREIGN KEY (gebied_id) REFERENCES gebied (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_AB4E87D8944D109 ON dienst (gebied_id)');
        $this->addSql('ALTER TABLE dienst ALTER COLUMN gebied_id SET NOT NULL');

        $this->addSql('ALTER TABLE ticket ADD gebied_id INT NULL');
        $this->addSql('UPDATE ticket SET gebied_id = (SELECT id FROM gebied ORDER BY id ASC LIMIT 1)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3944D109 FOREIGN KEY (gebied_id) REFERENCES gebied (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_97A0ADA3944D109 ON ticket (gebied_id)');
        $this->addSql('ALTER TABLE ticket ALTER COLUMN gebied_id SET NOT NULL');

        $this->addSql('ALTER TABLE onderneming ADD gebied_id INT NULL');
        $this->addSql('UPDATE onderneming SET gebied_id = (SELECT id FROM gebied ORDER BY id ASC LIMIT 1)');
        $this->addSql('ALTER TABLE onderneming ADD CONSTRAINT FK_4038A310944D109 FOREIGN KEY (gebied_id) REFERENCES gebied (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_4038A310944D109 ON onderneming (gebied_id)');
        $this->addSql('ALTER TABLE onderneming ALTER COLUMN gebied_id SET NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE onderneming DROP CONSTRAINT FK_4038A310944D109');
        $this->addSql('DROP INDEX IDX_4038A310944D109');
        $this->addSql('ALTER TABLE onderneming DROP gebied_id');
        $this->addSql('ALTER TABLE dienst DROP CONSTRAINT FK_AB4E87D8944D109');
        $this->addSql('DROP INDEX IDX_AB4E87D8944D109');
        $this->addSql('ALTER TABLE dienst DROP gebied_id');
        $this->addSql('ALTER TABLE ticket DROP CONSTRAINT FK_97A0ADA3944D109');
        $this->addSql('DROP INDEX IDX_97A0ADA3944D109');
        $this->addSql('ALTER TABLE ticket DROP gebied_id');
    }
}
