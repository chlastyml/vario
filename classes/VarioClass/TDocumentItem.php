<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 3. 11. 2017
 * Time: 10:59
 */

include_once dirname(__FILE__) . '/ObjectToArray.php';

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

    public function fill($orderDetail){
        $sqlGetProdcutVarioID = "SELECT `id_vario` FROM ". _DB_PREFIX_ . "product WHERE id_product = " . $orderDetail['product_id'];
        $varioID_product = Db::getInstance()->getRow($sqlGetProdcutVarioID)['id_vario'];

        $sqlGetItemVarioID = "SELECT `id_vario` FROM ". _DB_PREFIX_ . "product_attribute WHERE id_product_attribute = " . $orderDetail['product_attribute_id'];
        $varioID_item = Db::getInstance()->getRow($sqlGetItemVarioID)['id_vario'];

        $this->ID = '';
        $this->DocumentID = '';
        $this->DocumentOrderNumber = 1;
        $this->Description = "Document item 1";
        $this->ItemNumber = "";
        $this->Quantity = $orderDetail['product_quantity'];
        $this->QuantityUnit = "Ks";
        $this->GPL = null;
        $this->PricePerUnit = $orderDetail['unit_price_tax_incl'];
        $this->PriceWithoutVAT = $orderDetail['unit_price_tax_excl'];
        $this->TotalVAT = $orderDetail['total_price_tax_incl'];
        $this->TotalPrice = $orderDetail['total_price_tax_excl'];
        $this->VATRate = 21; // TODO napevno?
        $this->DiscountRate = 0;
        $this->VATType = "Základní"; // TODO napevno?
        $this->StoreID = "";
        $this->ProductID = $varioID_product; // TODO doplnit vario ID k produktu
        $this->VariantID = $varioID_item;
        $this->State = ''; // TODO stav?
        $this->OrderID = '';
        $this->DeliveryDate = null; // TODO datum dodani
        $this->QuantityGroups = array();
        $this->DeliveryNoteID = '';
        $this->DeliveryNoteItemID = '';
        $this->CommissionID = '';
        $this->CommissionItemID = '';
        $this->Note = '';
        $this->Data1 = '';
        $this->Data2 = '';
        $this->Number1 = 0;
        $this->Number2 = 0;
        $this->ExternID = $orderDetail['id_order_detail'];
    }
}