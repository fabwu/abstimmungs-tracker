<?php

namespace App;

use DateTimeImmutable;
use PDO;

class AbstimmungRepository
{
    private PDO $dbh;

    public function __construct(PDO $dbh)
    {
        $this->dbh = $dbh;
    }

    function insert(Abstimmung $abstimmung): Abstimmung
    {
        $sql = "INSERT INTO abstimmung (external_id, title, date) VALUES (:external_id, :title, :date)";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue(':external_id', $abstimmung->externalId);
        $stmt->bindValue(':title', $abstimmung->title);
        $stmt->bindValue(':date', $this->dateToString($abstimmung->date));
        $stmt->execute();
        $abstimmung->id = $this->dbh->lastInsertId();
        return $abstimmung;
    }

    function findByExternalId(string $externalId): ?Abstimmung
    {
       $sql = 'SELECT * FROM abstimmung WHERE external_id = ? LIMIT 1';
       $stmt = $this->dbh->prepare($sql);
       $stmt->execute([$externalId]);
       $row = $stmt->fetch();
       if ($row) {
           return $this->map($row);
       } else {
           return null;
       }
    }

    public function findNewerThan(DateTimeImmutable $date): array
    {

        $sql = 'SELECT * FROM abstimmung WHERE date > ?';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$this->dateToString($date)]);
        $rows = $stmt->fetchAll();
        $abstimmungen = [];
        foreach ($rows as $row) {
            $abstimmungen[] = $this->map($row);
        }
        return $abstimmungen;
    }

    private function map(mixed $row): Abstimmung
    {
        $abstimmung = new Abstimmung();
        $abstimmung->id = $row['id'];
        $abstimmung->externalId = $row['external_id'];
        $abstimmung->title = $row['title'];
        $abstimmung->date = DateTimeImmutable::createFromFormat(DATE_ATOM, $row['date']);
        return $abstimmung;
    }

    private function dateToString(DateTimeImmutable $date): string
    {
        return $date->setTime(0, 0)->format(DATE_ATOM);
    }
}
