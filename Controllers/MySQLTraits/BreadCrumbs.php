<?php

namespace Controllers\MySQLTraits;

trait BreadCrumbs
{
    public function getBreadCrumbs($uuid, $skipFirst = false)
    {
        $sql = "SELECT * FROM items WHERE uuid = '". $uuid . "'";
        $this->sql[] = $sql;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (count($rows) == 0) {
            return null;
        }

        if (count($rows) > 1) {
            $this->addError('Found many items with uuid: ' . $uuid);
            return null;
        }

        foreach($rows as $item) {
            if($skipFirst) {
                $this->result[] = $this->castFromDB($item);
            }

            $this->getBreadCrumbs($item['parent_uuid'], true);
        }


    }
}
