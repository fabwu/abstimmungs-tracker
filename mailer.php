<?php

use App\Abstimmung;
use App\AbstimmungRepository;
use App\User;
use App\VorlageRepository;

require 'vendor/autoload.php';

$user = new User();
$user->createdAt = new DateTimeImmutable('2023-12-31');

$dbh = new PDO('sqlite:db.sq3');
$abstimmungRepository = new AbstimmungRepository($dbh);
$vorlageRepository = new VorlageRepository($dbh);

$abstimmungIds = array_map(fn(Abstimmung $a): int => $a->id,$abstimmungRepository->findNewerThan($user->createdAt));
$vorlagen = $vorlageRepository->findByAbstimmungId($abstimmungIds);
var_dump($vorlagen);

//TODO
// 2. Send mail for all Vorlagen which are after the user registration date
