<?php

use App\Abstimmung;
use App\AbstimmungRepository;
use App\Database;
use App\User;
use App\VorlageRepository;

require 'vendor/autoload.php';

$user = new User();
$user->createdAt = new DateTimeImmutable('2023-12-31');

$database = new Database();
$abstimmungRepository = new AbstimmungRepository($database->dbh);
$vorlageRepository = new VorlageRepository($database->dbh);

$abstimmungIds = array_map(fn(Abstimmung $a): int => $a->id,$abstimmungRepository->findNewerThan($user->createdAt));
$vorlagen = $vorlageRepository->findByAbstimmungId($abstimmungIds);
var_dump($vorlagen);

//TODO
// 2. Send mail for all Vorlagen which are after the user registration date
