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
            $src = " AND src LIKE '%" . $requestArray['body']['src'] . "%'";
        } else {
            $src = '';
        }

        if(strlen($requestArray['body']['title']) > 0) {
            $title = " AND title LIKE '%" . $requestArray['body']['title'] . "%'";
        } else {
            $title = '';
        }

// tags: []
        // TODO: запит має бути з PDO:
        $sql = "SELECT * FROM items WHERE uuid IS NOT NULL"
                    . $type . $description . $src . $title
                    . " ORDER BY " . $requestArray['body']['order_by'] . " " . $requestArray['body']['sort']
                    . " LIMIT " . $requestArray['body']['limit']
                    . " OFFSET " . $requestArray['body']['offset'];

        $this->sql[] = $sql;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $this->result[] = $rows;
    }
}
