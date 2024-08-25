<?php

namespace App;

use PDO;

class Database
{
    public PDO $dbh;

    public function __construct()
    {
        $this->dbh = new PDO('sqlite:db.sq3');
    }
}
