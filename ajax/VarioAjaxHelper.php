<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 26. 10. 2017
 * Time: 15:38
 */

require_once('../classes/SoapMe.php');
require_once('../classes/ImportProduct.php');
require_once('../classes/ParentSetting.php');

class VarioAjaxHelper extends ParentSetting
{
    public function set_params($post){
        $wsdl_url = trim($post['wsdl_url']);

        $CONFIG_DIR_PATH = $this->getConfigDirPath();
        $CONFIG_PATH = $this->getConfigPath();

        $config = array(
            'wsdl_url' => $wsdl_url
        );

        if (!file_exists($CONFIG_DIR_PATH)){
            mkdir($CONFIG_DIR_PATH);
        }

        if (file_exists($CONFIG_PATH)){
            unlink($CONFIG_PATH);
        }

        $json = json_encode($config);

        $f = fopen($CONFIG_PATH, 'a+');
        fwrite( $f, print_r( $json, true ) . PHP_EOL );
        fclose( $f );
    }

    public function get_params(){
        $CONFIG_DIR_PATH = $this->getConfigDirPath();
        $CONFIG_PATH = $this->getConfigPath();

        if (!file_exists($CONFIG_DIR_PATH) OR !file_exists($CONFIG_PATH)){
            return null;
        }

        $string = file_get_contents($CONFIG_PATH, true);
        json_decode($string);
        if (json_last_error() == JSON_ERROR_NONE){
            return $string;
        }
        return null;
    }

    public function test_vario()
    {
        $wsdlUrl = $this->getWsdlUrl();
        $client = new SoapMe($wsdlUrl);
        return null;
    }

    public function import_product()
    {
        $wsdlUrl = $this->getWsdlUrl();

        $import = new ImportProduct($wsdlUrl);
        $result = $import->import_from_vario();

        if ($result !== 'END') {
            $logDirPath = $this->getLogDirPath();
            $logPath = $this->getLogPath();

            if (!file_exists($logDirPath)) {
                mkdir($logDirPath);
            }

            $f = fopen($logPath, 'a+');
            fwrite($f, print_r((new DateTime())->format('Y-m-d H:i:s'), true) . PHP_EOL);
            fwrite($f, print_r($result, true) . PHP_EOL);
            fwrite($f, print_r('----------------------------------------------------------------------------------------------------------', true) . PHP_EOL);
            fclose($f);
        }

        return $result;
    }

    public function export_order(){
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'orders`';
        $result = Db::getInstance()->executeS($sql);

        return count($result);
    }

    private function getWsdlUrl(){
        $jsonConfig = json_decode($this->get_params());

        if (property_exists($jsonConfig, 'wsdl_url')) {
            return $jsonConfig->wsdl_url;
        }

        throw new Exception('wsdl_url not found');
    }
}