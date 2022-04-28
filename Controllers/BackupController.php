<?php

namespace Controllers;

use Controllers\MySQLController;
use Controllers\ResponseController;
use Salabun\TelegramBotNotifier;


class BackupController
{
    /**
     *
     * @var Singleton
     */
    private static $instance;

    public $mysql = null;

    public static function makeBackup()
    {
        if ( is_null( self::$instance ) )
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->mysql = new MySQLController();

        //var_dump($this->mysql);
        var_dump($this->mysql->database);

        $this->sqlDump();
        $this->sqlZip();
        $this->sendToTelegram();
        // TODO: - зробити дамп БД, запаролити архів, надіслати в ТГ
    }

    public function sqlDump()
    {
        // filename: $this->mysql->database . ".sql";
        //  info: "info.txt"; // дата і інша метаінформація
    }

    public function sqlZip()
    {
        // pass from .ENV
    }

    public function sendToTelegram()
    {
        // pass from .ENV
    }

}
