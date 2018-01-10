<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Notitie;
use GemeenteAmsterdam\HeelEnSchoonBundle\Entity\Ticket;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170706230000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $stmtLedigingen = $this->connection->prepare('
            SELECT
                l.*,
                o.id AS onderneming_id,
                o.geo_point AS geo,
                o.straat AS straat,
                o.huisnummer AS nummer,
                b.uuid AS bak_uuid
            FROM legacy_lediging AS l
            LEFT JOIN ondernemers_bak AS b ON b.uuid = l.ondernemers_bak_uuid
            LEFT JOIN onderneming AS o ON b.onderneming_id = o.id
        ');
        $stmtNotities = $this->connection->prepare('
            SELECT
                n.*,
                o.straat AS straat,
                o.huisnummer AS nummer
            FROM legacy_notitie AS n
            LEFT JOIN onderneming AS o ON n.onderneming_id = o.id
            WHERE n.deleted = false
            AND n.hoofd_notitie_id IS NULL
        ');
        $stmtSubNotities = $this->connection->prepare('
            SELECT
                n.*
            FROM legacy_notitie AS n
            WHERE n.deleted = false
            AND n.hoofd_notitie_id = ?
            ORDER BY n.datum_tijd ASC
        ');
        $stmtNotitieFotos = $this->connection->prepare('
            SELECT
                nf.*
            FROM legacy_notitie_fotos AS nf
            WHERE nf.notitie_id = ?
        ');
        $stmtInsertTicket = $this->connection->prepare('
            INSERT INTO ticket (
                id,
                medewerker_id,
                onderneming_id,
                ondernemers_bak_uuid,
                bron,
                datum_tijd_aangemaakt,
                datum_tijd_gewijzigd,
                datum_tijd_gesloten,
                oplossing,
                geo,
                straat,
                huisnummer,
                tekst,
                type,
                status,
                verwijderd
            ) VALUES (
                nextval(\'ticket_id_seq\'),
                :medewerker_id,
                :onderneming_id,
                :ondernemers_bak_uuid,
                :bron,
                :datum_tijd_aangemaakt,
                :datum_tijd_gewijzigd,
                :datum_tijd_gesloten,
                :oplossing,
                :geo,
                :straat,
                :huisnummer,
                :tekst,
                :type,
                :status,
                false
            )
        ');
        $stmtInsertActie = $this->connection->prepare('
            INSERT INTO actie (
                id,
                medewerker_id,
                ticket_id,
                datum_tijd_aangemaakt,
                type,
                tekst
            ) VALUES (
                nextval(\'actie_id_seq\'),
                :medewerker_id,
                :ticket_id,
                :datum_tijd_aangemaakt,
                :type,
                :tekst
            )
        ');
        $stmtInsertTicketFoto = $this->connection->prepare('
            INSERT INTO notitie_fotos
            (notitie_id, foto_id)
            VALUES
            (:notitie_id, :foto_id)
        ');

        $stmtLedigingen->execute();
        $ledigingen = $stmtLedigingen->fetchAll();
        foreach ($ledigingen as $record) {
            $stmtInsertTicket->execute([
                ':medewerker_id' => $record['medewerker_id'],
                ':onderneming_id' => $record['onderneming_id'],
                ':ondernemers_bak_uuid' => $record['bak_uuid'],
                ':bron' => Ticket::BRON_HANDMATIG,
                ':datum_tijd_aangemaakt' => $record['datum_tijd'],
                ':datum_tijd_gewijzigd' => $record['datum_tijd'],
                ':datum_tijd_gesloten' => $record['datum_tijd'],
                ':oplossing' => Ticket::OPLOSSING_ANDERS,
                ':geo' => $record['geo'],
                ':straat' => $record['straat'],
                ':huisnummer' => $record['nummer'],
                ':type' => 'ledigingsverzoek',
                ':tekst' => null,
                ':status' => 1
            ]);
        }
        $stmtNotities->execute();
        $notities = $stmtNotities->fetchAll();
        foreach ($notities as $record) {
            $datumTijdGewijzigd = $datumTijdGesloten= null;
            if (isset($subNotities[0])) {
                $datumTijdGewijzigd = $subNotities[0]['datum_tijd'];
            }
            if ($record['flag'] == false && $datumTijdGewijzigd) {
                $datumTijdGewijzigd = $datumTijdGesloten;
            }

            $stmtInsertTicket->execute([
                ':medewerker_id' => $record['medewerker_id'],
                ':onderneming_id' => $record['onderneming_id'],
                ':ondernemers_bak_uuid' => null,
                ':bron' => Ticket::BRON_HANDMATIG,
                ':datum_tijd_aangemaakt' => $record['datum_tijd'],
                ':datum_tijd_gewijzigd' => $datumTijdGewijzigd,
                ':datum_tijd_gesloten' => $datumTijdGesloten,
                ':oplossing' => Ticket::OPLOSSING_ANDERS,
                ':geo' => $record['geo'],
                ':straat' => $record['straat'],
                ':huisnummer' => $record['nummer'],
                ':type' => 'notitie',
                ':tekst' => $record['tekst'],
                ':status' => $record['flag'] === true ? 0 : 1
            ]);

            // get the new id
            $id = $this->connection->lastInsertId('ticket_id_seq');

            // ophalen fotos
            $stmtNotitieFotos->execute([$record['id']]);
            $fotos = $stmtNotitieFotos->fetchAll();
            foreach ($fotos as $fotoRecord) {
                $stmtInsertTicketFoto->execute(['notitie_id' => $id, 'foto_id' => $fotoRecord['foto_id']]);
            }

            // ophalen subrecords
            $stmtSubNotities->execute([$record['id']]);
            $subNotities = $stmtSubNotities->fetchAll();
            foreach ($subNotities as $subNotitieRecord) {
                $stmtInsertActie->execute([
                    ':medewerker_id' => $subNotitieRecord['medewerker_id'],
                    ':ticket_id' => $id,
                    ':datum_tijd_aangemaakt' => $subNotitieRecord['datum_tijd'],
                    ':type' => 'notitie',
                    ':tekst' => $subNotitieRecord['tekst'],
                ]);

                // ophalen id
                $subId = $this->connection->lastInsertId('ticket_id_seq');

                // ophalen fotos
                $stmtNotitieFotos->execute([$subNotitieRecord['id']]);
                $fotos = $stmtNotitieFotos->fetchAll();
                foreach ($fotos as $fotoRecord) {
                    $stmtInsertTicketFoto->execute(['notitie_id' => $subId, 'foto_id' => $fotoRecord['foto_id']]);
                }
            }
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
