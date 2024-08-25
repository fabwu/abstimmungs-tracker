<?php

namespace App;

use DateTimeImmutable;

class User
{
    public int $id;
    public string $username;
    public DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }
}
