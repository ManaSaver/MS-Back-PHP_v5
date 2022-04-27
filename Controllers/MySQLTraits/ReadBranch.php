<?php

namespace Controllers\MySQLTraits;

trait ReadBranch
{
    public $rootBranchMaxDepth = 10;
    public $currentBranchMaxDepth = 1;

    public function getBranch($parentUUID = null, $isCategory = false)
    {
        if($this->hasErrors()) {
            return null;
        }

        // Для рекурсії будуть передаватись масив батьківських дописів:
        if(is_array($parentUUID)) {

            // Якщо масив порожній, то зупиняю програму:
            if(count($parentUUID) == 0) {
                return null;
            }

            if($isCategory) {
                $sql = "SELECT * FROM items WHERE type = 'category' AND";
            } else {
                $sql = "SELECT * FROM items WHERE ";
            }

            // Формую запит для пошуку дочірніх дописів до всіх батьківських за один раз:
            foreach($parentUUID as $key => $uuid) {
                $sql .= ($key == 0 ? "" : " OR") . " parent_uuid = '". $uuid . "'";
            }

        } else {
            // Якщо це перший запит:
            if($isCategory) {
                $sql = "SELECT * FROM items WHERE type = 'category' AND parent_uuid ". ($parentUUID == null ? 'IS NULL' : "= '" . $parentUUID . "'");
            } else {
                $sql = "SELECT * FROM items WHERE parent_uuid ". ($parentUUID == null ? 'IS NULL' : "= '" . $parentUUID . "'");
            }
        }

        // Зберігаю запит для дебагу:
        $this->sql[] = $sql;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (count($rows) == 0) {
            return null;
        }

        $parentUUIDs = [];

        foreach($rows as $item) {
            $this->result[] = $this->castFromDB($item);

            if($isCategory) {
                if ($item['type'] == 'category') {
                    $parentUUIDs[] = $item['uuid'];
                }
            } else {
                if($item['type'] == 'code') {
                    continue;
                }

                $parentUUIDs[] = $item['uuid'];
            }
        }

        // Це про всяк випадок, можна пришвидшити вибірку категорій з головної, щоб не тягнути все:
        if($isCategory) {
            if($this->currentBranchMaxDepth <= $this->rootBranchMaxDepth) {
                $this->currentBranchMaxDepth = $this->currentBranchMaxDepth + 1;
                $this->getBranch($parentUUIDs, $isCategory);
            }
        } else {
            $this->getBranch($parentUUIDs, $isCategory);
        }


    }

    public function getRecordsByParentUUID($parentUUID)
    {
        if($parentUUID == null) {
            $sql = "SELECT * FROM items WHERE parent_uuid IS NULL ORDER BY order_index ASC";
        } else {
            $sql = "SELECT * FROM items WHERE parent_uuid = '" . $parentUUID . "' ORDER BY order_index ASC";
        }

        $this->sql[] = $sql;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

}
