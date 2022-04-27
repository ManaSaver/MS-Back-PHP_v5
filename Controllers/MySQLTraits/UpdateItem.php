<?php

namespace Controllers\MySQLTraits;

trait UpdateItem
{
    public function updateItem($uuid = '', $requestArray = [], $log = true)
    {
        if($log) {
            if (array_key_exists('order_index', $requestArray)) {
                $this->updateOrderIndex($uuid, $requestArray);
                return null;
            }
        }

        // Знаходжу існуючий допис:
        $oldRecord = [];

        $sql = "SELECT * FROM items WHERE uuid = '". $uuid . "'";
        $this->sql[] = $sql;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Зберігаю допис, щоб потім записати у логах стару версію:
        foreach($rows as $item) {
            $oldRecord = $this->castFromDB($item);
        }

        // Обробляю новий запит:
        $date_utc = new \DateTime("now", new \DateTimeZone("UTC"));
        $date_utc->format('Y-m-d H:i:s');

        $array = [];
        $array['updated_at'] = $date_utc->format('Y-m-d H:i:s');

        foreach($requestArray as $key => $val) {
            if(in_array($key, $this->allowedFields)) {
                if(is_array($val)) {
                    $array[$key] = json_encode($val);
                } else {
                    $array[$key] = $val;
                }
            }
        }

        $params = [];
        foreach($array as $field => $value) {
            $params[] = $field.' = :' . $field;
        }

        try {
            $stmt = $this->pdo->prepare("UPDATE items SET ". implode(', ', $params)." WHERE uuid = '". $uuid . "'");
            $this->sql[] = $stmt->queryString;

            foreach($array as $field => &$value) {
                $stmt->bindParam(':' . $field, $value);
            }

            $stmt->execute();

        } catch(\PDOException $e) {
            $this->addError($e->getMessage());
        }

        if($log) {
            if (count($oldRecord) > 0) {
                $this->createRevision($oldRecord);
            }
        }
    }

    public function updateOrderIndex($uuid = '', $requestArray = [])
    {
        if(array_key_exists('parent_uuid', $requestArray)) {
            // error
        }

        $requestArray['order_index'] = (int) $requestArray['order_index'];

        $this->getSingleItem($requestArray['uuid']);

        $movedItem = $this->result[0];

        if($this->hasErrors()) {
            return null;
        }

        if($requestArray['order_index'] > $movedItem['order_index']) {
            $this->moveDown($requestArray['uuid'], $requestArray['parent_uuid'], $requestArray['order_index']);
        } else {
            $this->moveUp($requestArray['uuid'], $requestArray['parent_uuid'], $requestArray['order_index']);
        }

    }

    public function moveDown($uuid, $parentUUID, $orderIndex)
    {
        if($parentUUID == null) {
            $sql = "SELECT * FROM items WHERE parent_uuid IS NULL AND order_index = '" . $orderIndex . "'";
        } else {
            $sql = "SELECT * FROM items WHERE parent_uuid = '" . $parentUUID . "' AND order_index = '" . $orderIndex . "'";
        }

        $this->sql[] = $sql;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach($rows as $item) {
            $otherRecord = $this->castFromDB($item);
            $this->result[] = $otherRecord;
        }

        if(count($rows) == 1) {
            // down
            try {
                $stmt = $this->pdo->prepare("UPDATE items SET order_index = order_index - 1 WHERE uuid = '". $otherRecord['uuid'] . "'");
                $this->sql[] = $stmt->queryString;
                $stmt->execute();

            } catch(\PDOException $e) {
                $this->addError($e->getMessage());
            }
        }

        // up
        try {
            $stmt = $this->pdo->prepare("UPDATE items SET order_index = order_index + 1 WHERE uuid = '". $uuid . "'");
            $this->sql[] = $stmt->queryString;
            $stmt->execute();

        } catch(\PDOException $e) {
            $this->addError($e->getMessage());
        }
    }

    public function moveUp($uuid, $parentUUID, $orderIndex)
    {
        if($parentUUID == null) {
            $sql = "SELECT * FROM items WHERE parent_uuid IS NULL AND order_index = '" . $orderIndex . "'";
        } else {
            $sql = "SELECT * FROM items WHERE parent_uuid = '" . $parentUUID . "' AND order_index = '" . $orderIndex . "'";
        }

        $this->sql[] = $sql;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach($rows as $item) {
            $otherRecord = $this->castFromDB($item);
            $this->result[] = $otherRecord;
        }

        if(count($rows) == 1) {
            // down
            try {
                $stmt = $this->pdo->prepare("UPDATE items SET order_index = order_index + 1 WHERE uuid = '". $otherRecord['uuid'] . "'");
                $this->sql[] = $stmt->queryString;
                $stmt->execute();

            } catch(\PDOException $e) {
                $this->addError($e->getMessage());
            }
        }

        // up
        try {
            $stmt = $this->pdo->prepare("UPDATE items SET order_index = order_index - 1 WHERE uuid = '". $uuid . "'");
            $this->sql[] = $stmt->queryString;
            $stmt->execute();

        } catch(\PDOException $e) {
            $this->addError($e->getMessage());
        }
    }
}
