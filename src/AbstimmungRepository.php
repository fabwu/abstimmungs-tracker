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
        $stmt->bindParam(':external_id', $abstimmung->externalId);
        $stmt->bindParam(':title', $abstimmung->title);
        $stmt->bindValue(':date', $abstimmung->date->setTime(0, 0, 0)->format(DATE_ATOM));
        $stmt->execute();
        $abstimmung->id = $this->dbh->lastInsertId();
        return $abstimmung;
    }

    function findByExternalId(string $externalId): ?Abstimmung
    {
       $sql = 'SELECT id, external_id, title, date FROM abstimmung WHERE external_id = ? LIMIT 1';
       $stmt = $this->dbh->prepare($sql);
       $stmt->execute([$externalId]);
       $columns = $stmt->fetch();
       if ($columns) {
           $abstimmung = new Abstimmung();
           $abstimmung->id = $columns['id'];
           $abstimmung->externalId = $columns['external_id'];
           $abstimmung->title = $columns['title'];
           $abstimmung->date = DateTimeImmutable::createFromFormat(DATE_ATOM, $columns['date']);
           return $abstimmung;
       } else {
           return null;
       }
    }
}
