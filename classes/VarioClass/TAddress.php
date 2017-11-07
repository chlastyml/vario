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

    public function fill($order)
    {
        $lang_id = 1;

        $customer = new Customer($order->id_customer);
        $address = $customer->getAddresses($lang_id)[0];
        $country = new Country($address['id_country'], $lang_id);

        $this->ID = '';
        $this->AddressType = 'atPrimary';
        $this->AddressName = $address['firstname'] . ' ' . $address['lastname'];
        $this->Street = $address['address1'];
        $secondAddress = $address['address2'];
        if ($secondAddress !== ''){
            $this->Street .= "\r\n" . $address['address2'];
        }
        $this->City = $address['city'];
        $this->ZIP = $address['postcode'];
        $this->Country = $country->name;
        $this->CountryISO = $country->iso_code;
        $this->UseOnDocuments = null;

        $this->Address = $this->Street . "\r\n" . $this->ZIP . " " . $this->City . "\r\n" . $this->Country;
    }
}