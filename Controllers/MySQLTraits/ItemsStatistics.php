<?php

namespace Controllers\MySQLTraits;

trait ItemsStatistics
{
    public function itemsCount()
    {
        return $this->pdo->query('select count(*) from items')->fetchColumn();
    }

    public function revisionsCount()
    {
        return $this->pdo->query('select count(*) from revisions')->fetchColumn();
    }

    public function lastItemsUpdate()
    {
        $sql = "SELECT * FROM items ORDER BY updated_at DESC LIMIT 0, 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach($rows as $item) {
            return $item['updated_at'];
        }

        return '--';
    }


	
}
