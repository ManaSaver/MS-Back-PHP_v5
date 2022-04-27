<?php

namespace Controllers\MySQLTraits;

trait ReadItem
{
    public function getSingleItem($uuid)
    {
        $sql = "SELECT * FROM items WHERE uuid = '". $uuid . "'";
        $this->sql[] = $sql;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (count($rows) == 0) {
            $this->addError('Item not found.');
            return null;
        }

        if (count($rows) > 1) {
            $this->addError('Found many items with uuid: ' . $uuid);
            return null;
        }

        foreach($rows as $item) {
            $this->result[] = $this->castFromDB($item);
        }
    }
}
