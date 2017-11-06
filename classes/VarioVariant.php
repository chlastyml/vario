<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 17. 10. 2017
 * Time: 13:30
 */

class VarioVariant
{
    private $original = null;
    private $unique = '';
    private $sex = '';
    private $color = '';
    private $size = '';
    private $price = 0;
    private $code = '';
    private $code_id = '';
    private $vario_id = '';
    private $job_id = '';

    /**
     * VarioVariant constructor.
     */
    public function __construct($variant)
    {
        $this->original = $variant;
        $code = $variant->Data->Code;
        $this->code = $code;
        $this->vario_id = $variant->Job->ObjectID;
        $this->job_id = $variant->Job->ID;
        $this->unique = self::getUniqueFromCode($code);
        $this->sex = self::getSexFromCode($code);
        $this->color = self::getColorFromCode($code);
        $this->size = self::getSizeFromCode($code);
        $this->price = $variant->Data->Price;
        $this->code_id = self::getUniqueFromCode($this->code);
    }

    /**
     * @return null
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * @return string
     */
    public function getUnique()
    {
        return $this->unique;
    }

    /**
     * @return string
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
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
    public function getCodeId()
    {
        return $this->code_id;
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
    public function getJobId()
    {
        return $this->job_id;
    }

    public static function getUniqueFromCode($code){
        return self::explodeCode($code)[0];
    }

    public static function getSexFromCode($code){
        return self::explodeCode($code)[1];
    }

    public static function getColorFromCode($code){
        return self::explodeCode($code)[2];
    }

    public static function getSizeFromCode($code){
        return self::explodeCode($code)[3];
    }

    private static function explodeCode($code){
        $unique = explode('/', $code);
        return $unique;
    }
}