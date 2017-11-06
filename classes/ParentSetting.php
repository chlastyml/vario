<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 27. 10. 2017
 * Time: 14:19
 */

include_once dirname(__FILE__).'/Logger.php';

abstract class ParentSetting
{
    private $configDirPath = '';
    private $configPath = '';

    private $logger = null;

    public function __construct($loggerName = '')
    {
        $this->configDirPath = dirname(__FILE__) . '/../configuration';
        $this->configPath = $this->configDirPath . '/config.json';

        $this->logger = new Logger($loggerName);

        if (!file_exists($this->getConfigDirPath())){
            mkdir($this->getConfigDirPath());
        }
    }

    /**
     * @return string
     */
    public function getConfigDirPath()
    {
        return $this->configDirPath;
    }

    /**
     * @return string
     */
    public function getConfigPath()
    {
        return $this->configPath;
    }

    public function log($text){
        $this->logger->logLine($text);
    }
}