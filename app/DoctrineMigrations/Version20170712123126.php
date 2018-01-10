<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170712123126 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $stmt = $this->connection->prepare('SELECT ST_AsEWKT(geo) AS _geo, * FROM ticket WHERE straat IS NULL AND geo IS NOT NULL');
        $stmt->execute([]);
        $tickets = $stmt->fetchAll();

        $stmtUpdate = $this->connection->prepare('UPDATE ticket SET straat = :straat, huisnummer = :huisnummer WHERE id = :id');

        foreach ($tickets as $ticket) {
            // https://api.datapunt.amsterdam.nl/geosearch/search/?item=openbareruimte&lat=0&lon=&radius=50
            $matches = array();
            preg_match("/\((.*) (.*)\)/", $ticket['_geo'], $matches);

            $ch = curl_init('https://api.datapunt.amsterdam.nl/geosearch/search/?item=openbareruimte&lat=' . $matches[1] . '&lon=' . $matches[2] . '&radius=50');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $jsonString = curl_exec($ch);
            curl_close($ch);

            $jsonData = json_decode($jsonString);
            $jsonData->features= array_filter($jsonData->features, function ($row) {
                return $row->properties->opr_type == 'Weg';
            });
            if (count($jsonData->features) > 0) {
                $obj = reset($jsonData->features);
                $stmtUpdate->execute([':straat' => $obj->properties->display, ':huisnummer' => '', ':id' => $ticket['id']]);
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
