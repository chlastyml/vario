<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 18. 9. 2017
 * Time: 11:24
 */

include_once dirname(__FILE__).'/Helper.php';

class SoapMe
{
    public $error = null;

    private $wsdl = '';
    private $login = '';
    private $password = '';

    /** @var SoapClient */
    private $client = null;

    /**
     * SoapMe constructor.
     * @param $wsdl string
     * @param null $login
     * @param null $password
     * @throws Exception
     */
    public function __construct($wsdl = '', $login = null, $password = null)
    {
        if (Helper::IsNullOrEmptyString($wsdl))
            throw new Exception('wsdl nemuze byt null nebo prazny retezec');

        $this->wsdl = $wsdl;
        $this->login = $login;
        $this->password = $password;

        $this->refreshClient();
    }

    public function refreshClient(){
        if (!Helper::IsNullOrEmptyString($this->login) AND !Helper::IsNullOrEmptyString($this->password))
            $this->client = new SoapClient($this->wsdl, array('login' => $this->login, 'password' => $this->password));
        else
            $this->client = new SoapClient($this->wsdl);
    }

    // <editor-fold defaultstate="collapsed" desc="PRODUCT">

    public function getProducts()
    {
        try { // sending request
            return $this->client->GetProducts();
        } catch (SoapFault $fault) {
            $this->error = $fault;
        }
        return null;
    }

    public function getProductsArray($param = array(
        '45FA7928-2E70-4E04-8545-FFB1583E9B09',
        '362697FC-0CAA-4342-A41E-FFBEE3C36658',
        '7BA8A32A-0433-4623-BED0-FFBFB7EEDF7D',
        '8C3CD475-7E3D-42EE-92DB-FFC00355C541',
        'B9B14088-A3BC-4621-9ACD-FFC40C2B3B67',
        'D842E68A-7A3C-431A-B67F-FFCEB48DCD3F',
        '83B0B247-5C62-4CA5-B5DC-FFD7028E7C74',
        '50499D07-E5FE-46B5-8D36-FFDFA72C35CB',
        'C9869231-29AD-4F2B-A3F9-FFE36180B0DB',
        'B1B8059E-6240-4401-ADAE-FFE427E68886',
        '60F6D41A-BA0E-430F-854B-FFE885944FB5',
        'EF330E08-0875-4ED4-9D28-FFF11610B18B',
        '66056B92-9A51-4AC8-B97F-FFF5A83C80C6'
    ))
    {
        $response = null;

        try {
            $response = $this->client->GetProductsArray($param);
            return $response;
        } catch (SoapFault $fault) {
            $this->error = $fault;
        }// od catch

        return $response;
    }

    public function getProduct($param = '66056B92-9A51-4AC8-B97F-FFF5A83C80C6')
    {
        try { // sending request
            return $this->client->GetProduct($param);
        } catch (SoapFault $fault) {
            $this->error = $fault;
        }
        return null;
    }

    public function getProductExts($param = '7B78CE21-0A44-4495-8D92-00202F92272D')
    {
        try { // sending request
            return $this->client->GetProductExts($param);
        } catch (SoapFault $fault) {
            $this->error = $fault;
        }
        return null;
    }

    public function getProductSimple($param = '7B78CE21-0A44-4495-8D92-00202F92272D')
    {
        try { // sending request
            return $this->client->GetProductSimple($param);
        } catch (SoapFault $fault) {
            $this->error = $fault;
        }
        return null;
    }

    // </editor-fold>

    public function getJobsData($count, $objectType = 'otProductSimple')
    {
        try { // sending request
            return $this->client->GetJobsData($count, $objectType);
        } catch (SoapFault $fault) {
            $this->error = $fault;
        }
        return null;
    }

    public function getStores()
    {
        try { // sending request
            return $this->client->GetStores();
        } catch (SoapFault $fault) {
            $this->error = $fault;
        }
        return null;
    }

    public function getJobs()
    {
        try { // sending request
            return $this->client->GetJobs();
        } catch (SoapFault $fault) {
            $this->error = $fault;
        }
        return null;
    }

    public function getCategories()
    {
        try { // sending request
            return $this->client->GetCategories();
        } catch (SoapFault $fault) {
            $this->error = $fault;
        }
        return null;
    }

    public function getProductsByStore($store)
    {
        try { // sending request
            $first = $this->client->GetProductsByStore($store);

            $var = array();
            foreach ($first as $productId){
                if (!in_array($productId, $var)){
                    array_push($var, $productId);
                }
            }

            return $var;

            } catch (SoapFault $fault) {
            $this->error = $fault;
        }
        return null;
    }

    public function setJob($objectId, $done = true, $error = null)
    {
        try {
            return $this->client->SetJob($objectId, $done, $error);
        } catch (SoapFault $fault) {
            $this->error = $fault;
        }
        return false;
    }

    public function setJobs($doneArray, $done = true, $error = null)
    {
        try {
            return $this->client->SetJobs($doneArray, $done, $error);
        } catch (SoapFault $fault) {
            $this->error = $fault;
        }
        return false;
    }

    public function createOrUpdateDocument($orderDocument)
    {
        try {
            return $this->client->CreateOrUpdateDocument($orderDocument);
        } catch (SoapFault $fault) {
            $this->error = $fault;
            throw $fault;
        }
    }

    public function getFunctions()
    {
        try {
            return $this->client->__getFunctions();
        } catch (SoapFault $fault) {
            $this->error = $fault;
        }
        return false;
    }

    public function getStore($storeId)
    {
        try {
            return $this->client->GetStore($storeId);
        } catch (SoapFault $fault) {
            $this->error = $fault;
        }
        return false;
    }

    public function getPricelist($pricelistId)
    {
        try {
            return $this->client->GetPricelist($pricelistId);
        } catch (SoapFault $fault) {
            $this->error = $fault;
        }
        return false;
    }

    public function getPricelists()
    {
        try {
            return $this->client->GetPricelists();
        } catch (SoapFault $fault) {
            $this->error = $fault;
        }
        return false;
    }
}