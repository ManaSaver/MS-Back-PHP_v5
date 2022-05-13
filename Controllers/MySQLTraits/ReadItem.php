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

    public function getLastOrderIndex($parentUUID = null)
    {
        if($parentUUID == null) {
            $sql = "SELECT * FROM `items` WHERE `parent_uuid` IS NULL ORDER BY `order_index` DESC LIMIT 0, 1";
        } else {
            $sql = "SELECT * FROM `items` WHERE `parent_uuid` = '" . $parentUUID . "' ORDER BY `order_index` DESC LIMIT 0, 1";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (count($rows) == 1) {
            return $rows[0]['order_index'];
        }

        return 0;
    }
}
