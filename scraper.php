<?php

use App\Abstimmung;
use App\AbstimmungRepository;
use App\Vorlage;
use App\VorlageRepository;

require 'vendor/autoload.php';

const USE_DUMMY_DATA = false;

try {
    $dbh = new PDO('sqlite:db.sq3');
    $abstimmungRepository = new AbstimmungRepository($dbh);
    $vorlageRepository = new VorlageRepository($dbh);

    $overviewUrl = USE_DUMMY_DATA ? 'test-data/overview.json' :
        'https://ckan.opendata.swiss/api/3/action/package_show?id=echtzeitdaten-am-abstimmungstag-zu-eidgenoessischen-abstimmungsvorlagen';
    $abstimmungen = json_decode(loadJson($overviewUrl));
    foreach ($abstimmungen->result->resources as $idx => $abstimmungJson) {
        fwrite(STDOUT, sprintf("\rProcessing %s/%s", $idx + 1, count($abstimmungen->result->resources)));

        $abstimmungUrl = USE_DUMMY_DATA ? 'test-data/vorlage.json' : $abstimmungJson->url;
        $abstimmungDetails = json_decode(loadJson($abstimmungUrl));

        $dbh->beginTransaction();

        $abstimmung = $abstimmungRepository->findByExternalId($abstimmungJson->id);
        if ($abstimmung == null) {
            $abstimmung = new Abstimmung();
            $abstimmung->externalId = $abstimmungJson->id;
            $abstimmung->title = $abstimmungJson->title->de;
            $abstimmung->date = DateTimeImmutable::createFromFormat('Ymd', $abstimmungDetails->abstimmtag);
            $abstimmung = $abstimmungRepository->insert($abstimmung);
        }

        foreach ($abstimmungDetails->schweiz->vorlagen as $vorlageJson) {
            $vorlage = $vorlageRepository->findByExternalId($vorlageJson->vorlagenId);
            if ($vorlage == null) {
                $vorlage = new Vorlage();
                $vorlage->abstimmungId = $abstimmung->id;
                $vorlage->externalId = $vorlageJson->vorlagenId;
                $vorlage->title = $vorlageJson->vorlagenTitel[0]->text;
                $vorlage->vorlageAngenommen = $vorlageJson->vorlageAngenommen;
                $vorlageRepository->insert($vorlage);
            }
        }

        $dbh->commit();
    }
} catch (Exception $e) {
    var_dump($e);
}

function loadJson(string $overviewUrl): string
{
    $content = file_get_contents($overviewUrl);
    if ($content === false) {
        throw new Exception(error_get_last()['message']);
    }
    return $content;
}








