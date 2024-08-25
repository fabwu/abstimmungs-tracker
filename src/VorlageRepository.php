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
        $stmt->bindValue(':external_id', $vorlage->externalId);
        $stmt->bindValue(':title', $vorlage->title);
        $stmt->bindValue(':vorlage_angenommen', $vorlage->vorlageAngenommen, PDO::PARAM_BOOL);
        $stmt->execute();
        $vorlage->id = $this->dbh->lastInsertId();
        return $vorlage;
    }

    function findByExternalId(string $externalId): ?Vorlage
    {
        $sql = 'SELECT * FROM vorlage WHERE external_id = ? LIMIT 1';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$externalId]);
        $row = $stmt->fetch();
        if ($row) {
            return $this->map($row);
        } else {
            return null;
        }
    }

    public function findByAbstimmungId(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $inQuery = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "SELECT * FROM vorlage WHERE abstimmung_id IN ($inQuery)";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($ids);
        $rows = $stmt->fetchAll();
        $vorlagen = [];
        foreach ($rows as $row) {
            $vorlagen[] = $this->map($row);
        }
        return $vorlagen;
    }

    private function map(mixed $row): Vorlage
    {
        $vorlage = new Vorlage();
        $vorlage->id = $row['id'];
        $vorlage->externalId = $row['external_id'];
        $vorlage->title = $row['title'];
        $vorlage->vorlageAngenommen = $row['vorlage_angenommen'];
        return $vorlage;
    }
}
