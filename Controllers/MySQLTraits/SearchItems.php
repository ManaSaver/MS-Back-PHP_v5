<?php

namespace Controllers\MySQLTraits;

trait SearchItems
{
    public function searchItems($requestArray = [], $log = true)
    {
        if($requestArray['body']['type'] == 'all') {
            $type = '';
        } else {
            $type = " AND type = '" . $requestArray['body']['type'] . "'";
        }


        if(strlen($requestArray['body']['description']) > 0) {
            $description = " AND text LIKE '%" . $requestArray['body']['description'] . "%'";
        } else {
            $description = '';
        }

        if(strlen($requestArray['body']['src']) > 0) {

            if($requestArray['body']['type'] == 'code') {
                $src = " AND src = '" . $requestArray['body']['src'] . "'";
            } else {
                $src = " AND src LIKE '%" . $requestArray['body']['src'] . "%'";
            }

        } else {
            $src = '';
        }

        if(strlen($requestArray['body']['title']) > 0) {
            $title = " AND title LIKE '%" . $requestArray['body']['title'] . "%'";
        } else {
            $title = '';
        }





        $tags = '';
        if(count($requestArray['body']['tags']) > 0) {

            $unionsArray = [];

            foreach($requestArray['body']['tags'] as $tag) {

                $unionsArray[] = "(SELECT * FROM items WHERE uuid IS NOT NULL"
                    . $type . $description . $src . $title
                    . " AND JSON_SEARCH(`tags`, 'one', '%". trim($tag) . "%') IS NOT NULL)";

            }

            $sql = implode(' UNION ', $unionsArray)
                . " ORDER BY " . $requestArray['body']['order_by'] . " " . $requestArray['body']['sort']
                . " LIMIT " . $requestArray['body']['limit']
                . " OFFSET " . $requestArray['body']['offset'];

        } else {
            // TODO: запит має бути з PDO:
            $sql = "SELECT * FROM items WHERE uuid IS NOT NULL"
                . $type . $description . $src . $title . $tags
                . " ORDER BY " . $requestArray['body']['order_by'] . " " . $requestArray['body']['sort']
                . " LIMIT " . $requestArray['body']['limit']
                . " OFFSET " . $requestArray['body']['offset'];
        }

        $this->sql[] = $sql;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->result[] = $rows;



    }
}
