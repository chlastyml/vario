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
    private $client = null;

    public function __construct()
    {
        parent::__construct();
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
        try {
            $wsdlUrl = $this->getWsdlUrl();

            $import = new ImportProduct($wsdlUrl);
            $result = $import->import_from_vario();

            if ($result !== '') {
                $this->logTime($result);
            }

            $this->log("Import produktu z varia dokoncen." .
                "\n\rCelkovy pocet setJobData " . $import->getSetJobDataCount() . " a preskoceno " . $import->getSetJobDataSkipCount() . " zaznamu" .
                "\n\rWSDL: " . $wsdlUrl);

            return $result;
        }catch (Exception $exception){
            $this->log("Import produktu z varia. Kriticka chyba!\n\r " . $exception->getMessage());
            return "ERROR :" . $exception->getMessage();
        }
    }

    /**
     * @param $orderId int
     * @param $statusId Int
     */
    public function export_order($orderId, $statusId)
    {
        /*
         * ID Nazev                                         template
         ****************************************************************
         * 1  Čeká se na platbu šekem                       cheque
         * 2  Platba byla přijata                           payment
         * 3  Probíhá příprava                              preparation
         * 4  Odeslána                                      shipped
         * 5  Dodáno
         * 6  Zrušeno                                       order_canceled
         * 7  Splaceno                                      refund
         * 8  Chyba platby                                  payment_error
         * 9  U dodavatele (zaplaceno)                      outofstock
         * 10 Čeká se na přijetí bezhotovostní platby       bankwire
         * 11 Bezhotostní platba přijata                    payment
         * 12 U dodavatele (nezaplaceno)                    outofstock
         */

        if ($statusId == 2 OR $statusId == 11)
        {
            $order = new Order($orderId);

            // Convert na znamou entitu
            $document = new TDocument($order);

            try {
                $stdClass = $document->getStdClass();

                $varioID = $this->getClient()->createOrUpdateDocument($stdClass);

                // Aktualizace vario ids
                if (empty($document->ID)) {
                    $sqlInsert = 'UPDATE `' . _DB_PREFIX_ . 'orders` SET id_vario = \'' . $varioID . '\' WHERE id_order = ' . $order->id . ';';
                    Db::getInstance()->execute($sqlInsert);
                }

                $documentFromVario = $this->getClient()->getDocument($varioID);
                $loadDocumentItemsFromVario = $documentFromVario->DocumentItems;
                if (is_array($loadDocumentItemsFromVario))
                /** @var TDocumentItem $documentItem */
                foreach ($document->getDocumentItems() as $documentItem) {
                    foreach ($loadDocumentItemsFromVario as $documentItemFromVario) {
                        if ($documentItemFromVario->ExternID == $documentItem->ExternID AND empty($documentItem->ID)) {
                            $sqlInsert = 'UPDATE `' . _DB_PREFIX_ . 'order_detail` SET id_vario = \'' . $documentItemFromVario->ID . '\' WHERE id_order_detail = ' . $documentItem->ExternID . ';';
                            Db::getInstance()->execute($sqlInsert);
                        }
                    }
                }

                // TODO: Zmenit stav objednavky?

                $this->log("id_order: " . $order->id . " - Export objednavky do Varia dokoncen\r\nWSDL: " . $this->getWsdlUrl());
            } catch (Exception $exception) {
                $this->log("ERROR (send order to vario)\r\nid_order: " . $order->id . "\r\nMessage: " . $exception->getMessage());
            }
        }
    }

    public function download_invoice($orderId, $statusId)
    {
        /*
         * ID Nazev                                         template
         ****************************************************************
         * 1  Čeká se na platbu šekem                       cheque
         * 2  Platba byla přijata                           payment
         * 3  Probíhá příprava                              preparation
         * 4  Odeslána                                      shipped
         * 5  Dodáno
         * 6  Zrušeno                                       order_canceled
         * 7  Splaceno                                      refund
         * 8  Chyba platby                                  payment_error
         * 9  U dodavatele (zaplaceno)                      outofstock
         * 10 Čeká se na přijetí bezhotovostní platby       bankwire
         * 11 Bezhotostní platba přijata                    payment
         * 12 U dodavatele (nezaplaceno)                    outofstock
         */

        if ($statusId == 4) {
            $order = new Order($orderId);

            $vario_id = Db::getInstance()->getRow('SELECT o.id_vario FROM ' . _DB_PREFIX_ . 'orders o WHERE o.id_order = ' . $order->id)['id_vario'];

            if (!$vario_id){
                log('Download invoice: Nenalezeno vario ID objednavky');
                return;
            }

            $this->getClient()->getDocument($vario_id);

            //TODO get invoice pdf url
            $invoice_pdf_url = 'http://www.axmag.com/download/pdfurl-guide.pdf';

            $local_pdf = fopen( dirname( __FILE__ ) . '../invoices/' . $order->reference . '.pdf', 'w+' );
            $curl = curl_init( $invoice_pdf_url );
            //curl_setopt( $curl, CURLOPT_REFERER, 'https://www.sedi.ca/sedi/SVTWeeklySummaryACL?name=W1ALLPDFI&locale=en_CA');
            curl_setopt( $curl, CURLOPT_TIMEOUT, 60 );
            curl_setopt( $curl, CURLOPT_REFERER, $local_pdf );
            curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, 1 );
            curl_setopt( $curl, CURLOPT_ENCODING, "" );
            curl_exec( $curl );
            curl_close( $curl );
            fclose( $local_pdf );

            log('Download invoice: Dokonceno pro objednavku ID: ' . $orderId . ', Reference: ' . $order->reference);
        }
    }

    public function getWsdlUrl(){
        $jsonConfig = json_decode($this->get_params());

        if (property_exists($jsonConfig, 'wsdl_url')) {
            return $jsonConfig->wsdl_url;
        }

        throw new Exception('wsdl_url not found');
    }

    /**
     * @return SoapMe
     */
    public function getClient()
    {
        if ($this->client == null){
            $this->client = new SoapMe($this->getWsdlUrl());
        }

        return $this->client;
    }
}