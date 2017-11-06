<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 26. 10. 2017
 * Time: 15:38
 */

//require_once('../classes/SoapMe.php');
require_once('../classes/ImportProduct.php');
require_once('../classes/ParentSetting.php');
require_once('../classes/TDocument.php');
require_once('../classes/TDocumentItem.php');

class VarioHelper extends ParentSetting
{
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

        if ($result !== 'END') {
            $this->log($result);
        }

        return $result;
    }

    /**
     * @param $order Order
     * @return null
     */
    public function export_order($order)
    {
        /*
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

        $statusId = $order->current_state;

        if ($statusId == 2 OR
            $statusId == 11
        ) {
            $document = new TDocument();

            $document->ID = '';
            $document->Number = '';
            $document->Book = '';
            $document->DocumentName = 'Test dokladu objednavky nebo faktury';
            $document->DocumentType = 'ZZ';
            $document->Currency = 'CZK';
            $document->DontMakeInvoice = true;
            $document->VarNumber = 'test';
            $document->Comment = 'Comment';
            $document->Status = '';
            $document->Text = 'Test text';
            $document->Date = '2017-11-03T00:00:00.000+02:00';
            $document->TaxDate = null;
            $document->SettlementDate = '2017-11-03T00:00:00.000+02:00';
            $document->SettlementMethod = 'Bankovním převodem';
            $document->IO = 1;
            $document->TotalWithoutVAT = 123;
            $document->TotalWithVAT = 321;
            $document->Rounding = 0;
            $document->RequestedAdvance = 0;
            $document->AdvancePayed = 0;
            $document->Total = 231;
            $document->Payed = 0;
            $document->SettlementLeft = 0;
            $document->VATRoundingPlace = 0.01;
            $document->SumRoundingPlace = 1.00;
            $document->Interest = 0;
            $document->CompanyID = '{E5CD61F9-D824-4860-843E-A5E5ACFF9B75}';
            $document->DeliveryCompanyID = '';
            $document->CompanyName = 'TRIKATOR.CZ s.r.o.';
            $document->PersonName = '';
            $document->Addresses = '';
            $document->IC = '29448310';
            $document->DIC = 'CZ29448310';
            $document->Telephone = '';
            $document->Email = 'info@trikator.cz';
            $document->BankName = '';
            $document->BankBranch = '';
            $document->AccountNumber = '';
            $document->BankCode = '';
            $document->SpecificSymbol = '';
            $document->IBAN = '';
            $document->SalesAgent = 'Klára Štěpničková';
            $document->DueDateDays = 0;
            $document->Category = '';
            $document->PriceGroup = '';
            $document->PricelistID = '';
            $document->PricelistName = '';
            $document->Discount = '0';
            $document->Delivery = '';
            $document->OrderNumber = 'test';
            $document->OneDelivery = false;
            $document->Data1 = '';
            $document->Data2 = '';
            $document->Note = '';
            $document->UserFields = '';

            $orderDetailsArray = array();
            foreach ($order->getDocuments() as $orderDetail) {
                $documentDetail = new TDocumentItem();

                $documentDetail->ID = '';
                $documentDetail->DocumentID = '';
                $documentDetail->DocumentOrderNumber = 1;
                $documentDetail->Description = "Document item 1";
                $documentDetail->ItemNumber = "";
                $documentDetail->Quantity = 1;
                $documentDetail->QuantityUnit = "Ks";
                $documentDetail->GPL = 4;
                $documentDetail->PricePerUnit = 388.408;
                $documentDetail->PriceWithoutVAT = 388.41;
                $documentDetail->TotalVAT = 81.59;
                $documentDetail->TotalPrice = 470;
                $documentDetail->VATRate = 21;
                $documentDetail->DiscountRate = 0;
                $documentDetail->VATType = "Základní";
                $documentDetail->StoreID = "";
                $documentDetail->ProductID = "{EF330E08-0875-4ED4-9D28-FFF11610B18B}";
                $documentDetail->VariantID = '';
                $documentDetail->State = '';
                $documentDetail->OrderID = '';
                $documentDetail->DeliveryDate = null;
                $documentDetail->QuantityGroups = array();
                $documentDetail->DeliveryNoteID = '';
                $documentDetail->DeliveryNoteItemID = '';
                $documentDetail->CommissionID = '';
                $documentDetail->CommissionItemID = '';
                $documentDetail->Note = '';
                $documentDetail->Data1 = '';
                $documentDetail->Data2 = '';
                $documentDetail->Number1 = 0;
                $documentDetail->Number2 = 0;
                $documentDetail->ExternID = '';

                array_push($orderDetailsArray, $documentDetail->getArray());

                break;
            }

            $document->DocumentItems = $orderDetailsArray;

            try {
                $json = json_encode($document->getArray());

                $json = json_decode($json);

                $documentsFromVario = $this->client->getDocument();

                $json->Addresses = $documentsFromVario->Addresses;

                $result = $this->client->createOrUpdateDocument($json);
            } catch (Exception $exception) {
                return $exception->getMessage();
            }

            $varioID = $this->client->getDocument($result);

            $sqlInsert = 'UPDATE `' . _DB_PREFIX_ . 'orders` SET id_vario = \'' . $varioID . '\' WHERE id_order = ' . $order->id . ';';
            Db::getInstance()->execute($sqlInsert);
        }
        return null;
    }

    private function getWsdlUrl(){
        $jsonConfig = json_decode($this->get_params());

        if (property_exists($jsonConfig, 'wsdl_url')) {
            return $jsonConfig->wsdl_url;
        }

        throw new Exception('wsdl_url not found');
    }
}