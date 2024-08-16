<?php

namespace App;

use DateTimeImmutable;

class Abstimmung
{
    public int $id;
    public string $externalId;
    public string $title;
    public DateTimeImmutable $date;
}
