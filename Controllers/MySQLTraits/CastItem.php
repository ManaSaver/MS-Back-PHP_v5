<?php

namespace Controllers\MySQLTraits;


/**
 * Можна в базі зберігати всі поля як є, а не намагатись зберігати у мінімумі полів. А тут у касті видаляти зайві
 */
trait CastItem
{
    public function castFromDB($item)
    {
        $item['tags'] = json_decode($item['tags'], true);
        $item['order_index'] = (int) $item['order_index'];
        $item['likes'] = (int) $item['likes'];

        return $item;
    }

    public function castToDB($item)
    {
        $item['tags'] = json_encode($item['tags']);

        return $item;
    }
}
