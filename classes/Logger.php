<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 6. 11. 2017
 * Time: 14:47
 */

class Logger
{
    private $loggerName = '';

    private $logDirPath = '';
    private $logPath = '';

    public function __construct($loggerName = '')
    {
        if ($loggerName == ''){
            $this->loggerName = 'default';
            $this->logDirPath = dirname(__FILE__) . '/../logs';
        }else {
            if (!file_exists(dirname(__FILE__) . '/../logs')) {
                mkdir(dirname(__FILE__) . '/../logs');
            }
            $this->loggerName = $loggerName;
            $this->logDirPath = dirname(__FILE__) . '/../logs/' . $loggerName;
        }
        $this->logPath = $this->logDirPath . '/log.txt';

        if (!file_exists($this->logDirPath)) {
            mkdir($this->logDirPath);
        }
    }

    public function logLine($text){
        $f = fopen($this->logPath, 'a+');
        fwrite($f, print_r((new DateTime())->format('Y-m-d H:i:s'), true) . PHP_EOL);
        fwrite($f, print_r($text, true) . PHP_EOL);
        fwrite($f, print_r('----------------------------------------------------------------------------------------------------------', true) . PHP_EOL);
        fclose($f);
    }
}