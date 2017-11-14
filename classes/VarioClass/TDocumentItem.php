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

    public function __construct($object, $isOrderDetail){
        if ($isOrderDetail){
            $this->fillByOrderDetail($object);
        }else{
            $this->fillByCarrier($object);
        }
    }

    private function fillByOrderDetail($object){
        $orderDetail = $object->orderDetail;
        $documentOrderNumber = $object->documentOrderNumber;
        $tax_rate = $object->tax_rate;
        $vario_id_document = $object->vario_id_document;

        $sqlGetProdcutVarioID = "SELECT `id_vario` FROM ". _DB_PREFIX_ . "product WHERE id_product = " . $orderDetail['product_id'];
        $varioID_product = Db::getInstance()->getRow($sqlGetProdcutVarioID)['id_vario'];

        $sqlGetItemVarioID = "SELECT `id_vario` FROM ". _DB_PREFIX_ . "product_attribute WHERE id_product_attribute = " . $orderDetail['product_attribute_id'];
        $varioID_item = Db::getInstance()->getRow($sqlGetItemVarioID)['id_vario'];

        $sqlGetDocumentItemVarioID = "SELECT `id_vario` FROM ". _DB_PREFIX_ . "order_detail WHERE id_order_detail = " . $orderDetail['id_order_detail'];
        $vario_id_document_item = Db::getInstance()->getRow($sqlGetDocumentItemVarioID)['id_vario'];

        // (Polozky_dokladu.rowguid) ID položky dokladu, pokud se nepošle při založení, doplní se, při aktualizaci povinné
        $this->ID = $vario_id_document_item;
        // (Doklady.rowguid) ID dokladu, pokud se položka zapisuje samostatně, nutno vyplnit
        $this->DocumentID = $vario_id_document;
        // (Polozky_dokladu.Polozka_dokladu) číslo (pořadí) položky, v rámci dokladu musí být unikátní
        $this->DocumentOrderNumber = $documentOrderNumber; // TODO nemusi
        // (Polozky_dokladu.Popis) popis
        $this->Description = "";
        // (Polozky_dokladu.Cislo) (katalogové) číslo
        $this->ItemNumber = "";
        // (Polozky_dokladu.Mnozstvi) množství
        $this->Quantity = $orderDetail['product_quantity'];
        // (Polozky_dokladu.Jednotky) jednotka
        $this->QuantityUnit = "Ks"; // TODO napevno?
        // (Polozky_dokladu.Cena_zakladni) základní cena
        $this->GPL = null;
        // (Polozky_dokladu.Cena_za_jednotku) cena za jednotku, při zápisu nutno vyplnit
        $this->PricePerUnit = $orderDetail['unit_price_tax_incl'];
        // (Polozky_dokladu.Cena_bez_DPH) cena celkem bez DPH, při zápisu nutno vyplnit
        $this->PriceWithoutVAT = $orderDetail['unit_price_tax_excl'];
        // (Polozky_dokladu.DPH_celkem) celkem DPH, při zápisu nutno vyplnit
        $this->TotalVAT = $orderDetail['total_price_tax_incl'];
        // (Polozky_dokladu.Cena_s_DPH) celková cena včetně DPH, při zápisu nutno vyplnit
        $this->TotalPrice = $orderDetail['total_price_tax_excl'];

        // (Polozky_dokladu.Sazba_DPH) sazba DPH, při zápisu nutno vyplnit
        $this->VATRate = $tax_rate;
        // (Polozky_dokladu.Sleva_polozky) výše slevy
        $this->DiscountRate = 0;
        // (Polozky_dokladu.Zdanitelne_plneni) typ zdanitelného plnění (Základ daně, Z tuzemska, …)
        $this->VATType = "Základní"; // TODO napevno?
        // (Knihy.rowguid) ID skladu
        $this->StoreID = "";
        // (Katalog.rowguid) ID produktu
        $this->ProductID = $varioID_product; // TODO doplnit vario ID k produktu
        // (Katalog_varianty_produktu.rowguid) ID varianty
        $this->VariantID = $varioID_item;
        // (Polozky_dokladu.Stav) stav položky (DODAT, rezervovat, REZERVOVÁNO, fakturovat lze zapsat)
        $this->State = ''; // TODO stav?
        // (Doklady.rowguid) ID související zakázky (např. u faktur)
        $this->OrderID = '';
        // (Polozky_dokladu.Datum_dodani) (požadovaný) datum dodání
        $this->DeliveryDate = null; // TODO datum dodani
        // (Doklady.rowguid) ID dodacího listu (výdejky)
        $this->QuantityGroups = array();
        // (Polozky_dokladu.rowguid) ID položky dodacího listu (výdejky)
        $this->DeliveryNoteID = '';
        // (Doklady.rowguid) ID související zakázky
        $this->DeliveryNoteItemID = '';
        // (Polozky_dokladu.rowguid) ID související položky zakázky
        $this->CommissionID = '';
        // (Polozky_dokladu.Poznamka_polozky) poznámka
        $this->CommissionItemID = '';
        // (Polozky_dokladu.Poznamka_polozky) poznámka
        $this->Note = '';
        //
        $this->Data1 = '';
        //
        $this->Data2 = '';
        //
        $this->Number1 = 0;
        //
        $this->Number2 = 0;
        // volné pole pro případnou identifikaci položky shopem
        $this->ExternID = $orderDetail['id_order_detail'];
    }

    private function fillByCarrier($object){
        /** @var Carrier $carrier */
        $carrier = $object->carrier;
        $documentOrderNumber = $object->documentOrderNumber;
        $vario_id_document = $object->vario_id_document;
        /** @var Order $order */
        $order = $object->order;

        $vario_id_carrier = HellHelper::convertCarrierNameToVarioID($carrier->id_reference);

        $sqlOrderCarrier = "SELECT * FROM ". _DB_PREFIX_ . "order_carrier WHERE id_order = " . $order->id . " AND id_carrier = " . $carrier->id;
        $orderCarrier = Db::getInstance()->getRow($sqlOrderCarrier);

        $sqlGetOrderCarrierVarioID = "SELECT `id_vario_carrier` FROM ". _DB_PREFIX_ . "orders WHERE id_order = " . $order->id;
        $vario_id_document_item = Db::getInstance()->getRow($sqlGetOrderCarrierVarioID)['id_vario_carrier'];

        // (Polozky_dokladu.rowguid) ID položky dokladu, pokud se nepošle při založení, doplní se, při aktualizaci povinné
        $this->ID = $vario_id_document_item;
        // (Doklady.rowguid) ID dokladu, pokud se položka zapisuje samostatně, nutno vyplnit
        $this->DocumentID = $vario_id_document;
        // (Polozky_dokladu.Polozka_dokladu) číslo (pořadí) položky, v rámci dokladu musí být unikátní
        $this->DocumentOrderNumber = $documentOrderNumber; // TODO nemusi
        // (Polozky_dokladu.Popis) popis
        $this->Description = "";
        // (Polozky_dokladu.Cislo) (katalogové) číslo
        $this->ItemNumber = "";
        // (Polozky_dokladu.Mnozstvi) množství
        $this->Quantity = 1;
        // (Polozky_dokladu.Jednotky) jednotka
        $this->QuantityUnit = "Ks";
        // (Polozky_dokladu.Cena_zakladni) základní cena
        $this->GPL = null;
        // (Polozky_dokladu.Cena_za_jednotku) cena za jednotku, při zápisu nutno vyplnit
        $this->PricePerUnit = $orderCarrier['shipping_cost_tax_incl'];
        // (Polozky_dokladu.Cena_bez_DPH) cena celkem bez DPH, při zápisu nutno vyplnit
        $this->PriceWithoutVAT = $orderCarrier['shipping_cost_tax_excl'];
        // (Polozky_dokladu.DPH_celkem) celkem DPH, při zápisu nutno vyplnit
        $this->TotalVAT = $orderCarrier['shipping_cost_tax_incl'];
        // (Polozky_dokladu.Cena_s_DPH) celková cena včetně DPH, při zápisu nutno vyplnit
        $this->TotalPrice = $orderCarrier['shipping_cost_tax_excl'];

        // (Polozky_dokladu.Sazba_DPH) sazba DPH, při zápisu nutno vyplnit
        $this->VATRate = '';
        // (Polozky_dokladu.Sleva_polozky) výše slevy
        $this->DiscountRate = 0;
        // (Polozky_dokladu.Zdanitelne_plneni) typ zdanitelného plnění (Základ daně, Z tuzemska, …)
        $this->VATType = "Základní"; // TODO napevno?
        // (Knihy.rowguid) ID skladu
        $this->StoreID = "";
        // (Katalog.rowguid) ID produktu
        $this->ProductID = $vario_id_carrier; // TODO doplnit vario ID k produktu
        // (Katalog_varianty_produktu.rowguid) ID varianty
        $this->VariantID = '';
        // (Polozky_dokladu.Stav) stav položky (DODAT, rezervovat, REZERVOVÁNO, fakturovat lze zapsat)
        $this->State = ''; // TODO stav?
        // (Doklady.rowguid) ID související zakázky (např. u faktur)
        $this->OrderID = '';
        // (Polozky_dokladu.Datum_dodani) (požadovaný) datum dodání
        $this->DeliveryDate = null; // TODO datum dodani
        // (Doklady.rowguid) ID dodacího listu (výdejky)
        $this->QuantityGroups = array();
        // (Polozky_dokladu.rowguid) ID položky dodacího listu (výdejky)
        $this->DeliveryNoteID = '';
        // (Doklady.rowguid) ID související zakázky
        $this->DeliveryNoteItemID = '';
        // (Polozky_dokladu.rowguid) ID související položky zakázky
        $this->CommissionID = '';
        // (Polozky_dokladu.Poznamka_polozky) poznámka
        $this->CommissionItemID = '';
        // (Polozky_dokladu.Poznamka_polozky) poznámka
        $this->Note = 'Carrier';
        //
        $this->Data1 = '';
        //
        $this->Data2 = '';
        //
        $this->Number1 = 0;
        //
        $this->Number2 = 0;
        // volné pole pro případnou identifikaci položky shopem
        $this->ExternID = $order->id;
    }
}