<?php

namespace Controllers;

use Controllers\MySQLTraits\DataParams;
use Controllers\MySQLTraits\ReadBranch;
use Controllers\MySQLTraits\BreadCrumbs;

use Controllers\MySQLTraits\CreateItem;
use Controllers\MySQLTraits\ReadItem;
use Controllers\MySQLTraits\UpdateItem;
use Controllers\MySQLTraits\DeleteItem;

use Controllers\MySQLTraits\CastItem;
use Controllers\MySQLTraits\Revisions;

class MySQLController
{
    use CreateItem, DataParams, ReadBranch, CastItem, ReadItem, UpdateItem, Revisions, DeleteItem, BreadCrumbs;

    public $result = [];
    public $sql = [];


    public function __construct($database = null)
    {
        $this->connect($database);
	}

}
