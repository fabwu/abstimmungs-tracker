<?php

namespace App;

use PDO;

class VorlageRepository
{
    private PDO $dbh;

    public function __construct(PDO $dbh)
    {
        $this->dbh = $dbh;
    }

    function insert(Vorlage $vorlage): Vorlage
    {
        $sql = "INSERT INTO vorlage (abstimmung_id, external_id, title, vorlage_angenommen) 
                    VALUES (:abstimmung_id, :external_id, :title, :vorlage_angenommen)";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue(':abstimmung_id', $vorlage->abstimmungId);
        $stmt->bindParam(':external_id', $vorlage->externalId);
        $stmt->bindParam(':title', $vorlage->title);
        $stmt->bindValue(':vorlage_angenommen', $vorlage->vorlageAngenommen);
        $stmt->execute();
        $vorlage->id = $this->dbh->lastInsertId();
        return $vorlage;
    }

    function findByExternalId(string $externalId): ?Vorlage
    {
        $sql = 'SELECT id, abstimmung_id, external_id, title, vorlage_angenommen FROM vorlage WHERE external_id = ? LIMIT 1';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$externalId]);
        $columns = $stmt->fetch();
        if ($columns) {
            $vorlage = new Vorlage();
            $vorlage->id = $columns['id'];
            $vorlage->externalId = $columns['external_id'];
            $vorlage->title = $columns['title'];
            $vorlage->vorlageAngenommen = $columns['vorlage_angenommen'];
            return $vorlage;
        } else {
            return null;
        }
    }
}
