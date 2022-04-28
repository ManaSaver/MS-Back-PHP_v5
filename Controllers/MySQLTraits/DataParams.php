<?php

namespace Controllers\MySQLTraits;

trait DataParams
{
    public $pdo = null;
    public $host = '127.0.0.1';
    public $database = 'items_test';
    public $username = "root";
    public $password = "";

    public $allowedFields = ['parent_uuid', 'type', 'order_index', 'title', 'text', 'tags', 'src', 'likes'];

    public $errors = [];

    public function connect($database = null)
    {
        if($database != null) {
            $this->database = $database;
        }

        try {
            $this->pdo = new \PDO(
				'mysql:host=' . $this->host . ';dbname=' . 
				$this->database, $this->username, $this->password,
				[
					\PDO::ATTR_PERSISTENT => true
				]
			);
        } catch (\PDOException $e) {
            $this->addError($e->getMessage());
        }
    }

    public function addError($error)
    {
        $this->errors[] = $error;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        if(count($this->errors) > 0) {
            return true;
        }

        return false;
    }
}
