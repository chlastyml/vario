<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 3. 11. 2017
 * Time: 10:48
 */

include_once dirname(__FILE__).'/ObjectToArray.php';

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

    public function fill($order){
        $this->ID = '';
        $this->Number = '';
        $this->Book = '';
        $this->DocumentName = 'Test dokladu objednavky nebo faktury';
        $this->DocumentType = 'ZZ';
        $this->Currency = 'CZK';
        $this->DontMakeInvoice = false;
        $this->VarNumber = null;
        $this->Comment = '';
        $this->Status = '';
        $this->Text = 'Test text';
        $this->Date = '2017-11-03T00:00:00.000+02:00';
        $this->TaxDate = null;
        $this->SettlementDate = '2017-11-03T00:00:00.000+02:00';
        $this->SettlementMethod = 'Bankovním převodem';
        $this->IO = 1; //směr toku peněz (+1 faktura, pokladní příjmový doklad, výdejka, …, 0 stornovaný doklad, -1 dobropis, pokladní výdajový doklad, vratka výdejky, …)
        $this->TotalWithoutVAT = $order['total_paid_tax_excl'];
        $this->TotalWithVAT = $order['total_paid_tax_incl'];
        $this->Rounding = 0;
        $this->RequestedAdvance = 0;
        $this->AdvancePayed = 0;
        $this->Total = 231;
        $this->Payed = 0;
        $this->SettlementLeft = 0;
        $this->VATRoundingPlace = 0.01;
        $this->SumRoundingPlace = 1.00;
        $this->Interest = 0;
        $this->CompanyID = '';
        $this->DeliveryCompanyID = '';
        $this->CompanyName = 'TRIKATOR.CZ s.r.o.';
        $this->PersonName = '';
        $this->Addresses = '';
        $this->IC = '29448310';
        $this->DIC = 'CZ29448310';
        $this->Telephone = '';
        $this->Email = 'info@trikator.cz';
        $this->BankName = '';
        $this->BankBranch = '';
        $this->AccountNumber = '';
        $this->BankCode = '';
        $this->SpecificSymbol = '';
        $this->IBAN = '';
        $this->SalesAgent = 'Klára Štěpničková';
        $this->DueDateDays = 0;
        $this->Category = '';
        $this->PriceGroup = '';
        $this->PricelistID = '';
        $this->PricelistName = '';
        $this->Discount = '0';
        $this->Delivery = '';
        $this->OrderNumber = 'test';
        $this->OneDelivery = false;
        $this->Data1 = '';
        $this->Data2 = '';
        $this->Note = '';
        $this->UserFields = '';
    }
}






















