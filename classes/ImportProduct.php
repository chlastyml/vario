<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 25. 10. 2017
 * Time: 13:52
 */

include_once dirname(__FILE__) . '/SoapMe.php';
include_once dirname(__FILE__) . '/Hell_Helper.php';
include_once dirname(__FILE__) . '/AbstractClass/VarioProduct.php';
include_once dirname(__FILE__) . '/AbstractClass/VarioVariant.php';

class ImportProduct
{
    private $hasSentOnVario = false;

    /** @var SoapMe $client */
    private $client = null;

    /** @var array $bugRecords */
    private $bugRecords = array();

    private $setJobDataCount = 0;
    private $setJobDataSkipCount = 0;

    public function __construct($wsdl, $hasSentOnVario = false)
    {
        $this->hasSentOnVario = $hasSentOnVario;
        $this->client = new SoapMe($wsdl);

        $this->loadAttributes();
    }

    /**
     * @return int
     */
    public function getSetJobDataCount()
    {
        return $this->setJobDataCount;
    }

    /**
     * @return int
     */
    public function getSetJobDataSkipCount()
    {
        return $this->setJobDataSkipCount;
    }

    public function import_from_vario(){
        $this->bugRecords = array();

        $products = $this->client->getJobsData(3000);

        $this->setJobDataCount = count($products);

        $varioProducts = $this->convertInputOnAbstractObject($products);

        $varioProducts = $this->findAndConnectPrestaProducts($varioProducts);

        $this->saveToPresta($varioProducts);

        $result = '';
        foreach ($this->bugRecords as $bugRecord) {
            if ($this->bugRecords[0] == $bugRecord) {
                $result = $bugRecord;
            } else {
                $result .= "\r\n" . $bugRecord;
            }
        }
        return $result;
    }

    private function SendJobsComplete($skip_data)
    {
        if ($this->hasSentOnVario){
            $this->client->setJobs($skip_data);
        }
    }

    private $colorAttributes = array();
    private $sizeAttributes = array();
    private $cutAttributes = array();

    private function loadAttributes()
    {
        $this->colorAttributes = array();
        $this->sizeAttributes = array();
        $this->cutAttributes = array();

        $COLOR_NAME_EN = 'Color';
        $CUT_NAME_EN = 'Cut';
        $SIZE_NAME_EN = 'Size';

        $CUT_NAME_CS = 'Střih';
        $SIZE_NAME_CS = 'Velikost';
        $COLOR_NAME_CS = 'Barva';

        $attributes = Attribute::getAttributes(Hell_Helper::getCsLanguage());

        foreach ($attributes as $attribute) {
            $type = $attribute['attribute_group'];

            switch ($type){
                case $COLOR_NAME_CS:
                    array_push($this->colorAttributes, $attribute);
                    break;
                case $SIZE_NAME_CS:
                    array_push($this->sizeAttributes, $attribute);
                    break;
                case $CUT_NAME_CS:
                    array_push($this->cutAttributes, $attribute);
                    break;
            }
        }
    }

    /**
     * @param $products array
     * @return array
     */
    private function convertInputOnAbstractObject($products)
    {
        // Abstrakce nad vario produktem
        $varioProducts = array();
        foreach ($products as $product) {
            try {
                // Data bez Data nebo s neplatnym Bookem ignorujeme
                if ($product->Data == null OR $product->Data->Book !== 'Katalog Eshop') {
                    $this->setJobDataSkipCount++;
                    if ($product->Data == null){
                        array_push($this->bugRecords, 'CONVERT (SKIP): ' . trim($product->Job->ObjectID) . ', ' . $product->Job->Action);
                    }else {
                        array_push($this->bugRecords, 'CONVERT (SKIP): ' . $product->Data->Book . ', ' . trim($product->Data->ProductName));
                    }
                    continue;
                }

                $varioProduct = null;
                /** @var VarioProduct $item */
                foreach ($varioProducts as $item) {
                    $uniCode = $item->getUniqueFromCode($product);
                    $action = $item->getAction();

                    if ($uniCode == $item->getCode() AND $action == $product->Job->Action) {
                        if ($varioProduct == null) {
                            $varioProduct = $item;
                        } else {
                            throw new Exception('Duplicita');
                        }
                    }
                }

                if ($varioProduct == null) {
                    $varioProductNew = new VarioProduct($product);
                    array_push($varioProducts, $varioProductNew);
                } else {
                    $varioProduct->addNewItem($product);
                }
            } catch (Exception $exception) {
                array_push($this->bugRecords, 'CONVERT: ' . trim($product->Data->ProductName) . ': ' . $exception->getMessage());
                $this->setJobDataSkipCount++;
            }
        }

        return $varioProducts;
    }

    private function saveToPresta($varioProducts)
    {
        /** @var VarioProduct $varioProduct */
        foreach ($varioProducts as $varioProduct) {
            try {
                switch ($varioProduct->getAction()) {
                    case 'acInsert':
                    case 'acUpdate':
                        if (!$varioProduct->isReadyToSaveOrUpdate()) {
                            array_push($this->bugRecords, 'IMPORT (SKIP - Neni hlavni product ani nebyl nalezen produkt v prestashop): ' . $varioProduct->getCode());
                            continue;
                        }

                        // Zpracovani produktu
                        $varioProduct->createOrUpdate();

                        // Zpracovani varianty
                        $varioProduct->createOrUpdateVariant($this->colorAttributes, $this->sizeAttributes, $this->cutAttributes);

                        $this->SendJobsComplete($varioProduct->getSuccesJobIDs());
                        break;
                    case 'acDelete':
                        $varioProduct->delete();
                        break;
                }
            }catch (Exception $exception){
                array_push($this->bugRecords, 'IMPORT: ' . $varioProduct->getCode() . $varioProduct->getName() . ': ' . $exception->getMessage());
            }
        }
    }

    /**
     * @param $varioProduct VarioProduct
     * @return null|Product
     */
    private function tryFindExistProduct($varioProduct, $prestaProducts)
    {
        if ($varioProduct->getVarioId() == null OR $varioProduct->getVarioId() == ''){
            return null;
        }

        // Zkusim najit produkt podle varioID
        $sqlSelectProduct = "SELECT id_product FROM " . _DB_PREFIX_ . 'product WHERE id_vario = \'' . $varioProduct->getVarioId() . '\'';
        $varioID_item = Db::getInstance()->getRow($sqlSelectProduct);

        if ($varioID_item){
            return new Product($varioID_item['id_product']);
        }

        // Zkusim najit produkt podle code
        /** @var Product $prestaProduct */
        foreach ($prestaProducts as $prestaProduct) {
            $reference = $prestaProduct['reference'];
            if ($reference == $varioProduct->getCode()) {
                return new Product($prestaProduct['id_product']);
            }
        }
        return null;
    }

    /**
     * @param $prestaProduct Product
     * @param $varioVariant VarioVariant
     * @return int
     */
    private function tryFindExistVariant($prestaProduct, $varioVariant)
    {
        $combinationId = CombinationCore::getIdByReference($prestaProduct->id, $varioVariant->getCode());

        return $combinationId;
    }

    private function findAndConnectPrestaProducts($varioProducts){
        $prestaProducts = Product::getProducts(Hell_Helper::getCsLanguage(), 0, 0, 'id_product', 'DESC');
        /** @var VarioProduct $varioProduct */
        foreach ($varioProducts as $varioProduct) {
            $prestaProduct = $this->tryFindExistProduct($varioProduct, $prestaProducts);
            if ($prestaProduct !== null) {
                $varioProduct->setPrestaProduct($prestaProduct);

                /** @var VarioVariant $varioVariant */
                foreach ($varioProduct->getVariants() as $varioVariant) {
                    $combinationId = $this->tryFindExistVariant($prestaProduct, $varioVariant);

                    if ($combinationId !== null){
                        $varioVariant->setCombinationId($combinationId);
                    }
                }
            }

            if ($varioProduct->getMain() == null) {
                array_push($this->bugRecords, 'CONVERT (MAIN MISSING): ' . trim($varioProduct->getCode()));
            }
        }

        return $varioProducts;
    }
}