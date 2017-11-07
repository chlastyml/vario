<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 7. 11. 2017
 * Time: 18:32
 */

include_once dirname(__FILE__) . '/ObjectToArray.php';

class TAddress extends ObjectToArray
{
    public $ID;
    public $AddressType;
    public $AddressName;
    public $Address;
    public $Street;
    public $City;
    public $ZIP;
    public $Country;
    public $CountryISO;
    public $UseOnDocuments;

    public function fill($object)
    {
        // TODO: Implement fill() method.
    }
}