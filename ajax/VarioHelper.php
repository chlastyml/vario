<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 26. 10. 2017
 * Time: 15:38
 */

include_once dirname(__FILE__).'/../classes/SoapMe.php';
include_once dirname(__FILE__).'/../classes/ImportProduct.php';
include_once dirname(__FILE__).'/../classes/ParentSetting.php';
include_once dirname(__FILE__) . '/../classes/VarioClass/TDocument.php';
include_once dirname(__FILE__) . '/../classes/VarioClass/TDocumentItem.php';

class VarioHelper extends ParentSetting
{
    private $hasChange = false;

    private $client = null;

    public function __construct($initClient = false)
    {
        parent::__construct();

        if ($initClient) {
            $wsdlUrl = $this->getWsdlUrl();
            $this->client = new SoapMe($wsdlUrl);
        }
    }

    public function set_params($post){
        $wsdl_url = trim($post['wsdl_url']);

        $config = array(
            'wsdl_url' => $wsdl_url
        );

        $json = json_encode($config);

        if (file_exists($this->getConfigPath())){
            unlink($this->getConfigPath());
        }

        $CONFIG_PATH = $this->getConfigPath();
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

    public function import_product()
    {
        $wsdlUrl = $this->getWsdlUrl();

        $import = new ImportProduct($wsdlUrl);
        $result = $import->import_from_vario();

        if ($result !== '') {
            $this->logTime($result);
        }

        $this->log("Import produktu z varia dokoncen. \r\nWSDL: " . $wsdlUrl);

        return $result;
    }

    /**
     * @param $order Order
     * @param $statusId Int
     * @return null
     */
    public function export_order($order, $statusId)
    {
        /*
         * ID Nazev                             template
         ***********************************************
         * 1  Čeká se na platbu šekem           cheque
         * 2  Platba byla přijata               payment
         * 3  Probíhá příprava                  preparation
         * 4  Odeslána                          shipped
         * 5  Dodáno
         * 6  Zrušeno                           order_canceled
         * 7  Splaceno                          refund
         * 8  Chyba platby                      payment_error
         * 9  U dodavatele (zaplaceno)          outofstock
         * 10 Čeká se na přijetí bezhotovostní platby       bankwire
         * 11 Bezhotostní platba přijata        payment
         * 12 U dodavatele (nezaplaceno)        outofstock
         */

        if ($statusId == 2 OR $statusId == 11)
        {
            $document = new TDocument();

            $documentsFromVario = $this->client->getDocument();
            $document->fill($order, $documentsFromVario->Addresses);

            $document->DocumentItems = array();
            foreach ($order->getOrderDetailList() as $orderDetail) {

                $documentDetail = new TDocumentItem();
                $documentDetail->fill($orderDetail);

                array_push($document->DocumentItems, $documentDetail->getArray());
            }

            try {
                $stdClass = $document->getStdClass();

                if(!$this->hasChange) {
                    return null;
                }

                $varioID = $this->client->createOrUpdateDocument($stdClass);
            } catch (Exception $exception) {
                return $exception->getMessage();
            }

            // TODO nepotrebne
            $document = $this->client->getDocument($varioID);

            $sqlInsert = 'UPDATE `' . _DB_PREFIX_ . 'orders` SET id_vario = \'' . $varioID . '\' WHERE id_order = ' . $order->id . ';';
            Db::getInstance()->execute($sqlInsert);
        }
        return null;
    }

    public function load_order_invoice(){

    }

    public function getWsdlUrl(){
        $jsonConfig = json_decode($this->get_params());

        if (property_exists($jsonConfig, 'wsdl_url')) {
            return $jsonConfig->wsdl_url;
        }

        throw new Exception('wsdl_url not found');
    }
}