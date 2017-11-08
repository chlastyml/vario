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

    /**
     * TAddress constructor.
     * @param $customer Customer
     * @param $address Address
     */
    public function __construct($customer, $address)
    {
        $lang_id = 1;

        $country = new Country($address['id_country'], $lang_id);

        // (Firmy_adresy.rowguid) ID adresy (není nutno vyplňovat při založení adresy)
        $this->ID = '';
        // TAddressType = (atNone, atPrimary, atResidence, atDelivery, atAddress2, atOther)
        $this->AddressType = 'atPrimary';
        // (Firmy_adresy.Nazev_adresy) název adresy
        $this->AddressName = $address['firstname'] . ' ' . $address['lastname'];
        // (Firmy_adresy.Ulice_rozpis) ulice a číslo
        $this->Street = $address['address1'];
        $secondAddress = $address['address2'];
        if ($secondAddress !== ''){
            $this->Street .= "\r\n" . $address['address2'];
        }
        // (Firmy_adresy.Mesto_rozpis) město
        $this->City = $address['city'];
        // (Firmy_adresy.PSC_rozpis) PSČ
        $this->ZIP = $address['postcode'];
        // (Firmy_adresy.Stat_rozpis) Stát
        $this->Country = $country->name;
        // ISO země, doplňuje se podle Státu z číselníku
        $this->CountryISO = $country->iso_code;
        // (Firmy.Adresa_X_na_doklad, X – název adresy ve Variu) vkládat adresu na doklady
        $this->UseOnDocuments = null;

        // (Firmy_adresy.Adresa_rozpis) adresa ve formátu Ulice číslo<crlf>PSČ Město<crlf>Stát
        $this->Address = $this->Street . "\r\n" . $this->ZIP . " " . $this->City . "\r\n" . $this->Country;
    }
}