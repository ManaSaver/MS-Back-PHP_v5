<?php

namespace Controllers\MySQLTraits;

trait DeleteItem
{
    public $deleteBranchMaxDepth = 100;
    public $currentDeletingDepth = 1;

    /**
     * TODO: тут ще має бути стирання файлів
     *  А на оптимізацію похуй. Потім перепишу на Golang
     */
    public function deleteItem($request)
    {
        $oldRecord = [];

        if(!isset($request['uuid'])) {
            $this->addError('Specify uuid1.');
        }

        if($request['uuid'] == null) {
            $this->addError('UUID cannot be null.');
        }

        // Зчитую допис, який треба видалити:
        $sql = "SELECT * FROM items WHERE uuid = '". $request['uuid'] . "'";
        $this->sql[] = $sql;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach($rows as $item) {
            $oldRecord = $this->castFromDB($item);
            $this->result[] = $oldRecord;
        }

        // Логую:
        if (count($oldRecord) > 0) {
            $this->createRevision($oldRecord, true);
        }

        // Стираю цей допис:
        $sql = "DELETE FROM items WHERE uuid = '". $request['uuid'] . "'";
        $this->sql[] = $sql;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        // Пересортовую дописи у першій гілці:
        if($this->currentDeletingDepth == 1) {
            if(isset($this->result[0])) {
                $records = $this->getRecordsByParentUUID($this->result[0]['parent_uuid']);

                // error_log(json_encode($records));
                $orderIndex = 1;
                foreach ($records as $record) {
                    $record['order_index'] = $orderIndex;
                    $this->updateItem($record['uuid'], $record, false); // не логую оновлення порядку
                    $orderIndex++;
                }
            }
        }

        // Зчитую дочірні дописи, які теж треба видалити:
        $rows = $this->getRecordsByParentUUID($request['uuid']);


        $this->currentDeletingDepth++;

        foreach($rows as $item) {
            $this->deleteItem($item);
        }

    }

}
