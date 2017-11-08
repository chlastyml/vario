<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 3. 11. 2017
 * Time: 10:48
 */

include_once dirname(__FILE__) . '/ObjectToArray.php';
include_once dirname(__FILE__) . '/TAddress.php';

class TDocument extends ObjectToArray
{
    public $ID;
    public $Number;
    public $Book;
    public $DocumentName;
    public $DocumentType;
    public $Currency;
    public $DontMakeInvoice;
    public $VarNumber;
    public $Comment;
    public $Status;
    public $Text;
    public $Date;
    public $TaxDate;
    public $SettlementDate;
    public $SettlementMethod;
    public $IO;
    public $TotalWithoutVAT;
    public $TotalWithVAT;
    public $Rounding;
    public $RequestedAdvance;
    public $AdvancePayed;
    public $Total;
    public $Payed;
    public $SettlementLeft;
    public $VATRoundingPlace;
    public $SumRoundingPlace;
    public $Interest;
    public $CompanyID;
    public $DeliveryCompanyID;
    public $CompanyName;
    public $PersonName;
    public $Addresses;
    public $IC;
    public $DIC;
    public $Telephone;
    public $Email;
    public $BankName;
    public $BankBranch;
    public $AccountNumber;
    public $BankCode;
    public $SpecificSymbol;
    public $IBAN;
    public $SalesAgent;
    public $DueDateDays;
    public $Category;
    public $PriceGroup;
    public $PricelistID;
    public $PricelistName;
    public $Discount;
    public $Delivery;
    public $OrderNumber;
    public $OneDelivery;
    public $Data1;
    public $Data2;
    public $Note;
    public $UserFields;
    public $DocumentItems;

    /**
     * TDocument constructor.
     * @param $order Order
     */
    public function __construct($order)
    {
        $lang_id = 1;

        $customer = new Customer($order->id_customer);
        $currency = new Currency($order->id_currency);
        /** @var Address $address */
        $address = $customer->getAddresses($lang_id)[0];

        // (Doklady.rowguid) ID dokladu, pokud se nepošle při založení, doplní se, při aktualizaci povinné
        $this->ID = '';
        // (Doklady.Cislo_dokladu) číslo dokladu, pokud se nepošle při zápisu, doplní se podle číselné řady
        $this->Number = '';
        // (Doklady.Kniha) kniha, pokud se nepošle při zápisu, doplní se výchozí
        $this->Book = '';
        // (Doklady.Doklad) název dokladu (Zakázka, Zálohová faktura, Faktura, …)
        $this->DocumentName = '';
        // (Doklady.Typ_dokladu) typ dokladu, při zápisu nutno vyplnit (ZZ – zakázka, ZV – záloha vydaná, FV – faktura vydaná, SV – skladová výdejka, VV – vratka výdejky, PP – pokladní doklad příjmový, PV – pokladní doklad výdajový, …)
        $this->DocumentType = 'ZZ';  // TODO
        //  (Doklady.Mena) měna
        $this->Currency = $currency->iso_code;
        // (Doklady.Fakturovat) pokud je true, doklad se nemá fakturovat
        $this->DontMakeInvoice = true;
        // (Doklady.Variabilni_symbol) variabilní symbol (u typu dokladu ZZ se použije pole OrderNumber, pokud není vyplněno)
        $this->VarNumber = null; // TODO
        // (Doklady.Komentar) komentář
        $this->Comment = '';
        // (Doklady.Stav_dokladu) stav dokladu
        $this->Status = ''; // TODO mozne stavy
        // (Doklady.Text) text (obvykle se tiskne před položkami)
        $this->Text = ''; // TODO vyplnit?
        // (Doklady.Datum) datum vzniku dokladu
        $this->Date = date('Y-m-d\TH:i:s.vP', strtotime($order->date_add));
        // (Doklady.Datum_zdanitelneho_plneni) DUZP
        $this->TaxDate = null;
        // (Doklady.Datum_splatnosti) datum splatnosti
        $this->SettlementDate = date('Y-m-d\TH:i:s.vP', strtotime($order->date_add)); // TODO je to spravne?
        // (Doklady.Zpusob_uhrady) způsob úhrady
        $this->SettlementMethod = $order->payment;
        // (Doklady.PV) směr toku peněz (+1 faktura, pokladní příjmový doklad, výdejka, …, 0 stornovaný doklad, -1 dobropis, pokladní výdajový doklad, vratka výdejky, …)
        $this->IO = 1;
        // (Doklady.Celkem_bez_DPH) celkem bez DPH, při zápisu nutno zadat
        $this->TotalWithoutVAT = $order->total_paid_tax_excl;
        // (Doklady.Celkem_s_DPH) celkem s DPH, při zápisu nutno zadat
        $this->TotalWithVAT = $order->total_paid_tax_incl;
        // (Doklady.Zaokrouhleni) velikost zaokrouhlení
        $this->Rounding = 0; // TODO
        // (Doklady.Pozadovana_zaloha) požadovaná záloha
        $this->RequestedAdvance = 0; // TODO vyplnit?
        // (Doklady.Uhrazena_zaloha) zaplacená záloha
        $this->AdvancePayed = 0; // TODO vyplnit?
        // (Doklady.Celkem) celkem, při zápisu nutno zadat
        $this->Total = $order->total_paid;  // TODO celkam z dani? bez dane? zaokrouhlene?
        // (Doklady.Uhrazeno) zaplaceno
        $this->Payed = 0;  // TODO vyplnit?
        // (Doklady.Zbyva_uhradit) zbývá uhradit, při zápisu faktur nutno zadat
        $this->SettlementLeft = 0; // TODO vyplnit?
        // (Doklady.Zaokrouhlovat_DPH) zaokrouhlování DPH (např. 0,01 znamená na halíře)
        $this->VATRoundingPlace = 0.01;
        // (Doklady.Zaokrouhlovat_soucet) zaokrouhlování součtu (např. 1,00 znamená na koruny)
        $this->SumRoundingPlace = 0.01; // TODO vyplnit?
        // (Doklady.Urok) úrok z prodlení
        $this->Interest = 0; // TODO vyplnit?
        // (Firmy.rowguid) ID zákazníka (firmy), CustomerID
        $this->CompanyID = ''; // TODO doplnit
        // (Firmy.Kontakt_zbozi_dorucit), CustomerID pro zboží doručit
        $this->DeliveryCompanyID = ''; // TODO doplnit
        // (Doklady.Nazev_firmy) obchodní jméno zákazníka (Customer)
        $this->CompanyName = ''; // TODO doplnit
        // (Doklady.Jmeno) jméno osoby
        $this->PersonName = $customer->firstname . ' ' . $customer->lastname;
        // (Doklady.ICO) IČ
        $this->IC = ''; // TODO doplnit
        // (Doklady.DIC) DIČ
        $this->DIC = ''; // TODO doplnit
        // (Doklady.Telefon) telefon
        $this->Telephone = $this->getPhoneNumber($customer);
        // (Doklady.E_mail) e-mail
        $this->Email = $customer->email;
        // (Doklady.Banka) jméno banky, bankovní spojení
        $this->BankName = ''; // TODO
        //(Doklady.Pobocka) pobočka banky
        $this->BankBranch = ''; // TODO
        // (Doklady.Cislo_uctu) číslo účtu
        $this->AccountNumber = ''; // TODO
        // (Doklady.Kod_banky) kód banky
        $this->BankCode = ''; // TODO
        // (Doklady.Specificky_symbol) specifický symbol
        $this->SpecificSymbol = ''; // TODO
        //(Doklady.IBAN)
        $this->IBAN = ''; // TODO
        // (Doklady.Referent) referent
        $this->SalesAgent = '';  // TODO
        // (Doklady.Splatnost_dni) počet dní splatnosti
        $this->DueDateDays = 0; // TODO
        // (Doklady.Kategorie) kategorie dokladu (více hodnot oddělených středníkem)
        $this->Category = ''; // TODO
        // (Doklady.Kategorie_cen) kategorie cen dokladu (skupina ceníků, z těchto ceníků platí pro daný produkt nejvýhodnější cena)
        $this->PriceGroup = ''; // TODO
        // (Ceniky.rowguid) ID ceníku
        $this->PricelistID = ''; // TODO
        // (Doklady.Cenik) název ceníku
        $this->PricelistName = ''; // TODO
        // (Doklady.Sleva) název nebo výše slevy
        $this->Discount = '0'; // TODO
        // (Doklady.Zpusob_dopravy) způsob dopravy
        $this->Delivery = ''; // TODO
        // (Doklady.Objednavky) číslo objednávky (u typu dokladu ZZ, pokud není vyplněno, jde o Doklady.Variabilni_symbol) číslo objednávky
        $this->OrderNumber = ''; // TODO
        // (Doklady.Dodat_najednou) dodat najednou
        $this->OneDelivery = true; // TODO
        // (Doklady.Udaj_1) údaj 1
        $this->Data1 = ''; // TODO
        // (Doklady.Udaj_2) údaj 2
        $this->Data2 = ''; // TODO
        // (Doklady.poznamky) poznámka
        $this->Note = ''; // TODO
        // hodnoty uživatelských polí v XML formátu, např. <pole1>hodnota</pole1><pole2>hodnota</pole2>
        $this->UserFields = '';  // TODO Asi nevyplnovat

        // Adresa
        $this->Addresses = array();
        /** @var Address $address */
        foreach ($customer->getAddresses($lang_id) as $address){
            array_push($this->Addresses, new TAddress($customer, $address));
        }

        // TDocumentItems
        foreach ($order->getOrderDetailList() as $orderDetail) {
            $this->addDocumentItem(new TDocumentItem($orderDetail));
        }
    }

    /**
     * @param $documentItem TDocumentItem
     */
    private function addDocumentItem($documentItem){
        if ($this->DocumentItems == null OR !is_array($this->DocumentItems)){
            $this->DocumentItems = array();
        }

        array_push($this->DocumentItems, $documentItem->getArray());
    }

    /**
     * @param $customer Customer
     * @return null|string
     */
    private function getPhoneNumber($customer){
        $lang_id = 1;

        /** @var Address $address */
        foreach ($customer->getAddresses($lang_id) as $address) {
            $phone = $address['phone'];
            if ($phone !== null AND $phone !== '')
                return $phone;
        }
        return null;
    }
}
