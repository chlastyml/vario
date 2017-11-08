<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 25. 10. 2017
 * Time: 13:52
 */

include_once dirname(__FILE__).'/SoapMe.php';
include_once dirname(__FILE__).'/Helper.php';
include_once dirname(__FILE__).'/VarioProduct.php';
include_once dirname(__FILE__).'/VarioVariant.php';

class ImportProduct
{
    private $hasSentOnVario = false;

    /** @var SoapMe $client */
    private $client = null;

    /** @var int $csLanguage */
    private $csLanguage = -1;

    /** @var int $enLanguage */
    private $enLanguage = -1;

    /** @var array $bugRecords */
    private $bugRecords = array();

    private $setJobDataCount = 0;
    private $setJobDataSkipCount = 0;
    private $varioProductSkip = 0;
    private $productImportedCount = 0;
    private $combinationImportedCount = 0;

    public function __construct($wsdl, $hasSentOnVario = false)
    {
        $this->hasSentOnVario = $hasSentOnVario;
        $this->client = new SoapMe($wsdl);

        $this->loadLanguages();
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

    /**
     * @return int
     */
    public function getVarioProductSkip()
    {
        return $this->varioProductSkip;
    }

    /**
     * @return int
     */
    public function getProductImportedCount()
    {
        return $this->productImportedCount;
    }

    /**
     * @return int
     */
    public function getCombinationImportedCount()
    {
        return $this->combinationImportedCount;
    }

    public function import_from_vario(){
        $this->bugRecords = array();

        $products = $this->client->getJobsData(3000);

        $this->setJobDataCount = count($products);

        $varioProducts = $this->convertInputOnAbstractObject($products);

        /** @var VarioProduct $varioProduct */
        foreach ($varioProducts as $varioProduct) {
            if ($varioProduct->getMain() == null) {
                array_push($this->bugRecords, 'CONVERT (MAIN MISSING): ' . trim($varioProduct->getCode()));
            }
        }

        $this->saveToPresta($varioProducts);

        $result = '';
        foreach ($this->bugRecords as $bugRecord) {
            if ($this->bugRecords[0] == $bugRecord){
                $result = $bugRecord;
            }else {
                $result .= "\r\n" . $bugRecord;
            }
       }
        return $result;
    }

    private function generateSlug($input, $sexType = null){
        $input = $input . ' ' . $sexType;
        $input = Helper::remove_accents($input);
        $output = trim($input);

        $output = str_replace(' ', '-', $output);
        $output = str_replace('.', '', $output);
        $output = str_replace(',', '', $output);
        $output = str_replace('´', '', $output);
        $output = str_replace('\'', '', $output);
        $output = str_replace('&', 'AND', $output);

        $output = strtolower($output);

        return $output;
    }

    private function SendJobsComplete($skip_data)
    {
        if ($this->hasSentOnVario){
            $this->client->setJobs($skip_data);
        }
    }

    private function loadLanguages()
    {
        $languages = Language::getLanguages();

        foreach ($languages as $language) {
            $isoCode = $language['iso_code'];
            switch ($isoCode){
                case 'en':
                case 'gb':
                    $this->enLanguage = $language['id_lang'];
                    break;
                case 'cs':
                    $this->csLanguage = $language['id_lang'];
                    break;
            }
        }

        if ($this->enLanguage == -1){
            throw new Exception('Nenalezen anglicky jazyk');
        }

        if ($this->csLanguage == -1){
            throw new Exception('Nenalezen cesky jazyk');
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

        $attributes = Attribute::getAttributes($this->csLanguage);

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
        $skip_data = array();
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
                        //array_push($skip_data, $product->Job->ID);
                        array_push($this->bugRecords, 'CONVERT (SKIP): ' . $product->Data->Book . ', ' . trim($product->Data->ProductName));
                    }
                    continue;
                }

                $varioProduct = null;
                /** @var VarioProduct $item */
                foreach ($varioProducts as $item) {
                    $uniCode = $item->getUniqueFromCode($product);

                    if ($uniCode == $item->getCode()) {
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

        //Odeslat spatna data jako zpracovana
        $this->SendJobsComplete($skip_data);

        return $varioProducts;
    }

    private function saveToPresta($varioProducts)
    {
        $prestaProducts = Product::getProducts($this->csLanguage, 0, 0, 'id_product', 'DESC');

        /** @var VarioProduct $varioProduct */
        foreach ($varioProducts as $varioProduct) {
            $complete_vario_ids = array();

            try {
                /** @var Product $product */
                $product = $this->tryFindExistProduct($varioProduct, $prestaProducts);

                // Pokud neni hlavni produkt a produkt neni v databazi, nebo je poslana spatna struktura dat, tak jdu dal
                if ($varioProduct->getMain() == null){
                    if (!$varioProduct->isStructuralAlright() OR $product == null){
                        $this->varioProductSkip++;
                        array_push($this->bugRecords, 'IMPORT (SKIP): ' . $varioProduct->getCode());
                        continue;
                    }
                }

                // kontrala jestli produkt uz neexistuje
                if ($product == null) {
                    $product = $this->createAndFillProduct($varioProduct);
                    $product->save();
                    $this->productImportedCount++;
                }

                // Aktualizace vario ID
                $sqlInsert = 'UPDATE `' . _DB_PREFIX_ . 'product` SET id_vario = \'' . $varioProduct->getVarioId() . '\' WHERE id_product = ' . $product->id . ';';
                Db::getInstance()->execute($sqlInsert);

                // Hodit hlavni product jako zpracovany
                array_push($complete_vario_ids, $varioProduct->getJobId());

                // Tvorba kombinaci
                /** @var VarioVariant $varioVariant */
                foreach ($varioProduct->getVariants() as $varioVariant) {

                    $combinationId = CombinationCore::getIdByReference($product->id, $varioVariant->getCode());

                    if ($combinationId == null){
                        //tvorba nove kombinace
                        $color = Helper::transferColor($varioVariant->getColor());
                        $size = $varioVariant->getSize();
                        $sex = Helper::transferCut($varioVariant->getSex());

                        $colorAttribute = Helper::getAttribute($color, $this->colorAttributes);
                        $sizeAttribute = Helper::getAttribute($size, $this->sizeAttributes);
                        $sexAttribute = Helper::getAttribute($sex, $this->cutAttributes);

                        if ($colorAttribute == null OR $sizeAttribute == null OR $sexAttribute == null){
                            array_push($this->bugRecords, 'IMPORT VARIANT: ' . $varioProduct->getName() . ': '. $varioVariant->getCode() .
                                ': colorAttribute = ' . $color . ', sizeAttribute = ' . $size . ', sexAttribute = ' . $sex .
                                '!!! colorAttribute = ' . ($color !== null) . ', sizeAttribute = ' . ($size !== null) . ', sexAttribute = ' . ($sex !== null) );
                            continue;
                        }

                        $colorId = $colorAttribute['id_attribute'];
                        $sizeId = $sizeAttribute['id_attribute'];
                        $sexId = $sexAttribute['id_attribute'];

                        $idCom = $product->addCombinationEntity(
                            $varioVariant->getPrice(), 0, 0, 'unic_impact', 'ecotax', 1, null, $varioVariant->getCode(), null, null, null);

                        $combination = new Combination((int)$idCom);
                        $combination->setAttributes(array($sizeId, $colorId, $sexId));

                        $combinationId = CombinationCore::getIdByReference($product->id, $varioVariant->getCode());

                        // Aktualizace vario ID
                        $sqlInsert = 'UPDATE `' . _DB_PREFIX_ . 'product_attribute` SET id_vario = \'' . $varioVariant->getVarioId() . '\' WHERE id_product_attribute = ' . $combinationId . ';';
                        Db::getInstance()->execute($sqlInsert);

                        $this->combinationImportedCount++;

                    }else{
                        $sqlVario = 'SELECT id_vario FROM `' . _DB_PREFIX_ . 'product_attribute` WHERE id_product_attribute = ' . $combinationId;
                        $varioID = Db::getInstance()->executeS($sqlVario);
                        $varioID = $varioID[0]['id_vario'];

                        if ($varioID !== $varioVariant->getVarioId()) {
                            $sqlInsert = 'UPDATE `' . _DB_PREFIX_ . 'product_attribute` SET id_vario = \'' . $varioVariant->getVarioId() . '\' WHERE id_product_attribute = ' . $combinationId . ';';
                            Db::getInstance()->execute($sqlInsert);
                        }

                        $breakpoint = null;
                    }

                    array_push($complete_vario_ids, $varioVariant->getJobId());
                }

                $this->SendJobsComplete($complete_vario_ids);
            }catch (Exception $exception){
                array_push($this->bugRecords, 'IMPORT: ' . $varioProduct->getName() . ': ' . $exception->getMessage());
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
     * @param $varioProduct VarioProduct
     * @return Product
     */
    private function createAndFillProduct($varioProduct)
    {
        $product = new Product();

        $product->name = [$this->csLanguage => $varioProduct->getName()];
        $product->reference = $varioProduct->getCode();
        $product->link_rewrite = [$this->csLanguage => $this->generateSlug($varioProduct->getCode())];

        $product->active = false;

        return $product;
    }
}