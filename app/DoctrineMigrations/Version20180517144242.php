<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180517144242 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE notitie_fotos DROP CONSTRAINT FK_89B56F2D823C564');
        $this->addSql('ALTER TABLE ticket_categorie DROP CONSTRAINT FK_D3470C7E700047D2');
        $this->addSql('ALTER TABLE actie DROP CONSTRAINT FK_91FC74B8700047D2');

        $this->addSql('ALTER TABLE notitie_fotos ALTER notitie_id TYPE BIGINT USING (notitie_id::bigint)');
        $this->addSql('ALTER TABLE notitie_fotos ALTER notitie_id DROP DEFAULT');

        $this->addSql('ALTER TABLE ticket_categorie ALTER ticket_id TYPE BIGINT USING (ticket_id::bigint)');
        $this->addSql('ALTER TABLE ticket_categorie ALTER ticket_id DROP DEFAULT');

        $this->addSql('ALTER TABLE actie ALTER ticket_id TYPE BIGINT USING (ticket_id::bigint)');
        $this->addSql('ALTER TABLE actie ALTER ticket_id DROP DEFAULT');

        $this->addSql('ALTER TABLE ticket ALTER id TYPE BIGINT USING (id::bigint)');
        $this->addSql('ALTER TABLE ticket ALTER id DROP DEFAULT');

        $this->addSql('ALTER TABLE notitie_fotos ADD CONSTRAINT FK_89B56F2D823C564 FOREIGN KEY (notitie_id) REFERENCES ticket (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket_categorie ADD CONSTRAINT FK_D3470C7E700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE actie ADD CONSTRAINT FK_91FC74B8700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ticket_categorie ALTER ticket_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE ticket_categorie ALTER ticket_id DROP DEFAULT');
        $this->addSql('ALTER TABLE ticket ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE ticket ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE notitie_fotos ALTER notitie_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE notitie_fotos ALTER notitie_id DROP DEFAULT');
        $this->addSql('ALTER TABLE actie ALTER ticket_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE actie ALTER ticket_id DROP DEFAULT');
    }
}
