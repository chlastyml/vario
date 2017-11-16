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
        if (empty($loggerName)){
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

    public function logLine($text, $onOneLine = false, $newOnTop = true){
        $logPath = $this->logDirPath . '/' . (new DateTime())->format('d-m-Y') . '.txt';

        $backupText = file_get_contents($logPath);

        unlink($logPath);

        $f = fopen($logPath, 'a+');

        if ($onOneLine){
            if ($newOnTop) {
                $resultText = print_r((new DateTime())->format('Y-m-d H:i:s') . ': ' . $text . $backupText, true);
            }else{
                $resultText = print_r($backupText . (new DateTime())->format('Y-m-d H:i:s') . ': ' . $text, true);
            }
        }else {
            if ($newOnTop) {
                $resultText = print_r(
                    (new DateTime())->format('Y-m-d H:i:s') . "\r\n" .
                    $text . "\r\n" .
                    '----------------------------------------------------------------------' . "\r\n" .
                    $backupText , true);
            }else{
                $resultText = print_r(
                    $backupText . "\r\n" .
                    (new DateTime())->format('Y-m-d H:i:s') . "\r\n" .
                    $text . "\r\n" .
                    '----------------------------------------------------------------------', true);
            }
        }

        fwrite($f, print_r($resultText, true) . PHP_EOL);
        fclose($f);
    }

    public function logLineByNewFile($fileName, $text, $onOneLine = false, $newOnTop = true){
        $logPath = $this->logDirPath . '/' . $fileName . '.txt';

        $backupText = file_get_contents($logPath);

        unlink($logPath);

        $f = fopen($logPath, 'a+');

        if ($onOneLine){
            if ($newOnTop) {
                $resultText = print_r((new DateTime())->format('Y-m-d H:i:s') . ': ' . $text . $backupText, true);
            }else{
                $resultText = print_r($backupText . (new DateTime())->format('Y-m-d H:i:s') . ': ' . $text, true);
            }
        }else {
            if ($newOnTop) {
                $resultText = print_r(
                    (new DateTime())->format('Y-m-d H:i:s') . "\r\n" .
                    $text . "\r\n" .
                    '----------------------------------------------------------------------' . "\r\n" .
                    $backupText , true);
            }else{
                $resultText = print_r(
                    $backupText . "\r\n" .
                    (new DateTime())->format('Y-m-d H:i:s') . "\r\n" .
                    $text . "\r\n" .
                    '----------------------------------------------------------------------', true);
            }
        }

        fwrite($f, print_r($resultText, true) . PHP_EOL);
        fclose($f);
    }

    public function logLineByTimeFile($text){
        $pathDir = $this->logDirPath . '/' . (new DateTime())->format('Y-m-d');
        if (!file_exists($pathDir)) {
            mkdir($pathDir);
        }
        $path = $pathDir . '/' . (new DateTime())->format('H-i-s') . '.txt';
        $f = fopen($path, 'a+');
        fwrite($f, print_r($text, true) . PHP_EOL);
        fclose($f);
    }
}