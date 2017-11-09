<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 17. 10. 2017
 * Time: 12:03
 */

include_once dirname(__FILE__).'/VarioVariant.php';
include_once dirname(__FILE__).'/Helper.php';

class VarioProduct
{
    private $MAIN_CODE = 'ZB';
    private $VARIANT_CODE = 'VY';

    private $main = null;
    private $variants = array();
    private $code = null;

    private $job_id = '';
    private $vario_id = '';

    private $action = '';

    private $prestaProduct;

    /**
     * VarioProduct constructor.
     */
    public function __construct($product)
    {
        $this->code = $this->getUniqueFromCode($product);
        $this->action = $product->Job->Action;

        $this->addItem($product);
    }

    public function addNewItem($product){
        $unigueCode = $this->getUniqueFromCode($product);

        if ($this->code != $unigueCode){
            throw new Exception('Nesouhlsai');
        }

        $this->addItem($product);
    }

    private function addItem($product){
        $type = $product->Data->ProductType;

        switch ($type){
            case $this->MAIN_CODE :
                if ($this->main != null) {
                    $var = null;
                }
                $this->job_id = $product->Job->ID;
                $this->vario_id = $product->Job->ObjectID;
                $this->action = $product->Job->Action;
                $this->main = $product;
                break;
            case $this->VARIANT_CODE :
                $variant = new VarioVariant($product);
                array_push($this->variants, $variant);
                break;
            default:
                $var = null;
        }
    }

    public function getUniqueFromCode($product){
        $code = $product->Data->Code;
        $unique = explode('/', $code);
        return $unique[0];
    }

    public function isReadyToSaveOrUpdate()
    {
        return $this->getMain() == null AND $this->getPrestaProduct() == null;
    }

    private $succesJobIDs = array();

    public function createOrUpdate(){
        if ($this->getPrestaProduct() == null) { // Tvorime novy

            $product = new Product();

            $product->name = [Helper::getCsLanguage() => $this->getName()];
            $product->reference = $this->getCode();
            $product->link_rewrite = [Helper::getCsLanguage() => Helper::generateSlug($this->getCode())];

            $product->active = false;

            $this->setPrestaProduct($product);
            $this->getPrestaProduct()->save();
        }else{ // Aktualizujeme
            // TODO
        }

        // Aktualizace vario ID
        $sqlInsert = 'UPDATE `' . _DB_PREFIX_ . 'product` SET id_vario = \'' . $this->getVarioId() . '\' WHERE id_product = ' . $this->getPrestaProduct()->id . ';';
        Db::getInstance()->execute($sqlInsert);

        array_push($this->succesJobIDs, $this->getJobId());
    }

    public function createOrUpdateVariant($colorAttributes, $sizeAttributes, $cutAttributes){
        /** @var VarioVariant $varioVariant */
        foreach ($this->getVariants() as $varioVariant) {

            $combinationId = $varioVariant->getCombinationId();

            if ($combinationId == null){ // Tvorba kombinace
                //tvorba nove kombinace
                $color = Helper::transferColor($varioVariant->getColor());
                $size = $varioVariant->getSize();
                $sex = Helper::transferCut($varioVariant->getSex());

                $colorAttribute = Helper::getAttribute($color, $colorAttributes);
                $sizeAttribute = Helper::getAttribute($size, $sizeAttributes);
                $sexAttribute = Helper::getAttribute($sex, $cutAttributes);

                if ($colorAttribute == null OR $sizeAttribute == null OR $sexAttribute == null){
                    /*
                    array_push($this->bugRecords, 'IMPORT VARIANT: ' . $this->getName() . ': '. $varioVariant->getCode() .
                        ': colorAttribute = ' . $color . ', sizeAttribute = ' . $size . ', sexAttribute = ' . $sex .
                        '!!! colorAttribute = ' . ($color !== null) . ', sizeAttribute = ' . ($size !== null) . ', sexAttribute = ' . ($sex !== null) );
                    */
                    continue;
                }

                $colorId = $colorAttribute['id_attribute'];
                $sizeId = $sizeAttribute['id_attribute'];
                $sexId = $sexAttribute['id_attribute'];

                $idCom = $this->getPrestaProduct()->addCombinationEntity(
                    $varioVariant->getPrice(), 0, 0, 'unic_impact', 'ecotax', 1, null, $varioVariant->getCode(), null, null, null);

                $combination = new Combination((int)$idCom);
                $combination->setAttributes(array($sizeId, $colorId, $sexId));

                $combinationId = CombinationCore::getIdByReference($this->getPrestaProduct()->id, $varioVariant->getCode());
            }else{ // Aktualizace
                // TODO
            }

            $sqlVario = 'SELECT id_vario FROM `' . _DB_PREFIX_ . 'product_attribute` WHERE id_product_attribute = ' . $combinationId;
            $varioID = Db::getInstance()->executeS($sqlVario);
            $varioID = $varioID[0]['id_vario'];

            if ($varioID !== $varioVariant->getVarioId()) {
                $sqlInsert = 'UPDATE `' . _DB_PREFIX_ . 'product_attribute` SET id_vario = \'' . $varioVariant->getVarioId() . '\' WHERE id_product_attribute = ' . $combinationId . ';';
                Db::getInstance()->execute($sqlInsert);
            }

            array_push($this->succesJobIDs, $varioVariant->getJobId());
        }
    }

    public function delete(){
        $prestaProduct = $this->getPrestaProduct();
        if ($prestaProduct !== null){
            //$prestaProduct->active = false;
            $prestaProduct->update(array(
                'active' => false
            ));
        }
    }

    public function getSuccesJobIDs(){
        return $this->succesJobIDs;
    }

    public function getMain()
    {
        return $this->main;
    }

    /**
     * @return array
     */
    public function getVariants()
    {
        return $this->variants;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if ($this->main == null)
            return 'no name';

        return trim($this->main->Data->ProductName);
    }

    /**
     * @return string
     */
    public function getJobId()
    {
        return $this->job_id;
    }

    /**
     * @return string
     */
    public function getVarioId()
    {
        return $this->vario_id;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return Product
     */
    public function getPrestaProduct()
    {
        return $this->prestaProduct;
    }

    /**
     * @param Product $prestaProduct
     */
    public function setPrestaProduct($prestaProduct)
    {
        $this->prestaProduct = $prestaProduct;
    }
}