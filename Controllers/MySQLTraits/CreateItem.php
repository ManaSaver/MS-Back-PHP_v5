<?php

namespace Controllers\MySQLTraits;

trait CreateItem
{

    public function createItem($requestArray = [])
    {
        $date_utc = new \DateTime("now", new \DateTimeZone("UTC"));
        $date_utc->format('Y-m-d H:i:s');

        $array = [];
        $array['uuid'] = $this->createUUID();
        $array['created_at'] = $date_utc->format('Y-m-d H:i:s');
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

        // Посуваю наступні елементи у цій гілці:
        try {
            $stmt = $this->pdo->prepare("UPDATE items SET order_index = order_index + 1 WHERE order_index >= ". $array['order_index'] . " AND parent_uuid " . ($array['parent_uuid'] == null ? 'IS NULL' : "= '" . $array['parent_uuid'] . "'"));
            $stmt->execute();
            $this->sql[] = $stmt->queryString;
        } catch(\PDOException $e) {
            $this->addError($e->getMessage());
            return null;
        }

        // Створюю новий допис:
        $params = [];
        foreach($array as $field => $value) {
            $params[] = $field.' = :' . $field;
        }

        try {
            $stmt = $this->pdo->prepare("INSERT INTO items SET ". implode(', ', $params));
            $this->sql[] = $stmt->queryString;

            foreach($array as $field => &$value) {
                $stmt->bindParam(':' . $field, $value);
            }

            $stmt->execute();

        } catch(\PDOException $e) {
            $this->addError($e->getMessage());
            return null;
        }

        $this->result[] = $array;

        /*
        // Зчитую?
        $sql = "SELECT * FROM items WHERE parent_uuid ". ($array['parent_uuid'] == null ? 'IS NULL' : "= '" . $array['parent_uuid'] . "'");

        // Зберігаю запит для дебагу:
        $this->sql[] = $sql;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (count($rows) == 0) {
            return null;
        }

        foreach($rows as $item) {
            $this->result[] = $this->castFromDB($item);
        }
        */

        // $this->result[] = $this->castFromDB($item);
    }

    public function createUUID()
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

}
