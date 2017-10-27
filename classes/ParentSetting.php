<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 27. 10. 2017
 * Time: 14:19
 */

abstract class ParentSetting
{
    private $configDirPath = '';
    private $configPath = '';

    private $logDirPath = '';
    private $logPath = '';

    public function __construct()
    {
        $this->configDirPath = dirname(__FILE__) . '/../configuration';
        $this->configPath = $this->configDirPath . '/config.json';
        $this->logDirPath = dirname(__FILE__) . '/../logs';;
        $this->logPath = $this->logDirPath . '/log.txt';
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

    /**
     * @return string
     */
    public function getLogDirPath()
    {
        return $this->logDirPath;
    }

    /**
     * @return string
     */
    public function getLogPath()
    {
        return $this->logPath;
    }
}