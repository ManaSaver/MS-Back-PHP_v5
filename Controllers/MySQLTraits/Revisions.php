<?php

namespace Controllers\MySQLTraits;

trait Revisions
{
    public function createRevision($array, $deleted = false)
    {
        // Створюю новий допис:
        $params = [];
        foreach($array as $field => $value) {
            $params[] = $field.' = :' . $field;

            if (is_array($value)) {
                $array[$field] = json_encode($value);
            } else {
                $array[$field] = $value;
            }
        }

        if($deleted) {
            $array['deleted'] = 1;
            $params[] = 'deleted = :deleted';
        }

        $date_utc = new \DateTime("now", new \DateTimeZone("UTC"));
        $date_utc->format('Y-m-d H:i:s');

        $array['revision_at'] = $date_utc->format('Y-m-d H:i:s');
        $params[] = 'revision_at = :revision_at';


        try {
            $stmt = $this->pdo->prepare("INSERT INTO revisions SET ". implode(', ', $params));
            $this->sql[] = $stmt->queryString;

            foreach($array as $field => &$value) {
                $stmt->bindParam(':' . $field, $value);
            }

            $stmt->execute();

        } catch(\PDOException $e) {
            $this->addError($e->getMessage());
            return null;
        }
    }
}
