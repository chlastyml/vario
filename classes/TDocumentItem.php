<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 3. 11. 2017
 * Time: 10:59
 */

include_once dirname(__FILE__).'/ObjectToArray.php';

class TDocumentItem extends ObjectToArray
{
    public $ID;
    public $DocumentID;
    public $DocumentOrderNumber;
    public $Description;
    public $ItemNumber;
    public $Quantity;
    public $QuantityUnit;
    public $GPL;
    public $PricePerUnit;
    public $PriceWithoutVAT;
    public $TotalVAT;
    public $TotalPrice;
    public $VATRate;
    public $DiscountRate;
    public $VATType;
    public $StoreID;
    public $ProductID;
    public $VariantID;
    public $State;
    public $OrderID;
    public $DeliveryDate;
    public $QuantityGroups;
    public $DeliveryNoteID;
    public $DeliveryNoteItemID;
    public $CommissionID;
    public $CommissionItemID;
    public $Note;
    public $Data1;
    public $Data2;
    public $Number1;
    public $Number2;
    public $ExternID;
}