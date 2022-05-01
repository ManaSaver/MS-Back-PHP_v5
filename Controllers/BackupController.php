<?php

namespace Controllers;

use Controllers\MySQLController;
use Controllers\ResponseController;
use Salabun\TelegramBotNotifier;

use Rah\Danpu\Dump;
use Rah\Danpu\Export;

error_reporting(error_reporting() & ~E_NOTICE);


class BackupController
{
    /**
     *
     * @var Singleton
     */
    private static $instance;

    public $mysql = null;
    public $dumpDescription = '';

    public static function makeBackup($database = null)
    {
        if ( is_null( self::$instance ) )
        {
            self::$instance = new self($database);
        }
        return self::$instance;
    }

    public function __construct($database = null)
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 1));
        $dotenv->load();

        $this->mysql = new MySQLController($database);

        $this->sqlDump();
        $this->makedumpDescription();
        $this->sqlZip();
        $this->sendToTelegram();
        // $this->sendToEmail();
    }




    public function sqlDump()
    {
        try {
            $dump = new Dump;
            $dump
                ->file(dirname(__DIR__, 1) . '\\' . $this->mysql->database . '.sql')
                ->dsn('mysql:dbname=' . $this->mysql->database . ';host=' . $this->mysql->host)
                ->user($this->mysql->username)
                ->pass($this->mysql->password)
                ->tmp('/tmp');

            new Export($dump);
        } catch (\Exception $e) {
            echo 'Export failed with message: ' . $e->getMessage();
        }
    }

    public function makedumpDescription()
    {
        $this->dumpDescription =
            "Date: " . date("Y-m-d | h:i:s") . PHP_EOL .
            "Time Zone: " . date_default_timezone_get() . PHP_EOL .
                PHP_EOL .
            "Database: " . $this->mysql->database . PHP_EOL .
            "Records: " . $this->mysql->itemsCount() . PHP_EOL .
            "Revisions: " . $this->mysql->revisionsCount() . PHP_EOL .
            "Last update: " . $this->mysql->lastItemsUpdate() . PHP_EOL .
                PHP_EOL .
            "PC_NAME: " . env('PC_NAME') . PHP_EOL;
            "VERSION: " . env('VERSION') . PHP_EOL;
    }

    // DOC: https://github.com/Ne-Lexa/php-zip/blob/HEAD/README.RU.md#zipfilesetpasswordentry
    public function sqlZip()
    {
        $zipFile = new \PhpZip\ZipFile();

        try{
            $zipFile

                // dump info without password:
                ->addFromString(
                    'info.txt',
                    $this->dumpDescription
                )

                // dump with password:
                ->addFile(
                    dirname(__DIR__, 1) . '\\' . $this->mysql->database . '.sql',
                    $this->mysql->database . '.sql'
                )
                ->setPasswordEntry($this->mysql->database . '.sql', env('ZIP_PASSWORD'))

                ->saveAsFile($this->mysql->database. '.zip')
                ->setCompressionLevel(\PhpZip\Constants\ZipCompressionLevel::MAXIMUM)
                ->close(); // закрыть архив
        }
        catch(\PhpZip\Exception\ZipException $e){
            // обработка исключения
        }
        finally{
            $zipFile->close();
        }
    }

    public function sendToTelegram()
    {
        $telegram = new TelegramBotNotifier(env('TELEGRAM_BOT_TOKEN'));
        $telegram->addRecipient(env('TELEGRAM_CHAT_ID'));


        $telegram->addFile(realpath(dirname(__DIR__, 1) . '\\' . $this->mysql->database. '.zip'), function() {
            $telegram = new TelegramBotNotifier();
            $telegram
                ->text('📦 Резервна копія:')->br()
                ->bold('Mana Saver version 5')->br()
                ->br()
                ->br()
                ->text($this->dumpDescription)
                ->br()
                ->text('#mana_saver_version' . env('VERSION'));

            return $telegram->getText();
        });

        $telegram->sendDocument();

    }

    public function sendToEmail()
    {

    }
}
