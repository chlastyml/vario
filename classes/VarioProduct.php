<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 17. 10. 2017
 * Time: 12:03
 */

include_once dirname(__FILE__).'/VarioVariant.php';

class VarioProduct
{
    private $MAIN_CODE = 'ZB';
    private $VARIANT_CODE = 'VY';

    private $main = null;
    private $variants = array();
    private $code = null;

    private $vario_id = '';

    /**
     * VarioProduct constructor.
     */
    public function __construct($product)
    {
        $this->code = $this->getUniqueFromCode($product);

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
                $this->vario_id = $product->Job->ID;
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

    public function isStructuralAlright(){
        /** @var VarioVariant $variant */
        foreach ($this->variants as $variant) {
            if ($variant->getCodeId() != $this->code){
                return false;
            }
        }
        return true;
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
    public function getVarioId()
    {
        return $this->vario_id;
    }
}