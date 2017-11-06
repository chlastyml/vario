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
}






















