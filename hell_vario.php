<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 9. 10. 2017
 * Time: 10:11
 */

if (!defined('_PS_VERSION_')){
    exit;
}

include_once dirname(__FILE__) . '/classes/SoapMe.php';
include_once dirname(__FILE__) . '/classes/Helper.php';
include_once dirname(__FILE__) . '/ajax/VarioHelper.php';

class Hell_Vario extends Module
{
    public function __construct()
    {
        $this->name = 'hell_vario';
        $this->tab = 'export';
        $this->version = '0.8.8.9';
        $this->author = 'Hellit';
        $this->controllers = array('vario');
        $this->need_instance = 1;
        $this->ps_versions_compliancy = [ 'min' => '1.7.1.0', 'max' => _PS_VERSION_ ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName          = $this->l('Vario Export/Import');
        $this->description          = $this->l('Connect to Altus Vario.');
        $this->confirmUninstall     = $this->l('Are you sure you want to delete these details?');

        if (!Configuration::get('vario')) {
            $this->warning = $this->l('No name provided');
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() ||
            !$this->registerHook('leftColumn') ||
            !$this->registerHook('header') ||
            !Configuration::updateValue('vario', 'my friend') ||
            !$this->registerHook( 'actionOrderStatusUpdate' ) ||
            !$this->registerHook( 'actionOrderEdited' ) ||
            !$this->registerHook( 'displayPDFInvoice' ) ||
            !$this->installModuleTab()
        ) {
            return false;
        }

        $sqlPA = 'ALTER TABLE ' . _DB_PREFIX_ . 'product_attribute ADD `id_vario` VARCHAR(255)';
        $sqlO = 'ALTER TABLE ' . _DB_PREFIX_ . 'orders ADD `id_vario` VARCHAR(255)';
        $sqlP = 'ALTER TABLE ' . _DB_PREFIX_ . 'product ADD `id_vario` VARCHAR(255)';
        $sqlOD = 'ALTER TABLE ' . _DB_PREFIX_ . 'order_detail ADD `id_vario` VARCHAR(255)';
        $db = Db::getInstance();
        try {
            $db->Execute( $sqlPA);
        }catch (Exception $exception){
            $e = $exception;
        }
        try {
            $db->Execute( $sqlO);
        }catch (Exception $exception){
            $e = $exception;
        }
        try {
            $db->Execute( $sqlP);
        }catch (Exception $exception){
            $e = $exception;
        }
        try {
            $db->Execute( $sqlOD);
        }catch (Exception $exception){
            $e = $exception;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !Configuration::deleteByName('vario') ||
            !$this->uninstallModuleTab()
        ) {
            return false;
        }

        return true;
    }

    public function installModuleTab(){
        $tab = new Tab();
        $tab->active = 1;
        $langs = language::getLanguages();
        foreach ($langs as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Vario');
        }
        $tab->id_parent = 2;
        $tab->position = 6;
        $tab->module = $this->name;
        $tab->class_name = 'AdminVario';

        $this->tab = $tab;

        return $tab->save();
    }

    public function uninstallModuleTab(){
        $id_tab = Tab::getIdFromClassName('AdminVario');
         if ($id_tab) {
             $tab = new Tab($id_tab);
             return $tab->delete();
         }
        return true;
    }

    public function hookActionOrderStatusUpdate( $params )
    {
        $newOrderStatus = $params['newOrderStatus'];
        $statusId = $newOrderStatus->id;

        /*
         * ID Nazev                                         template
         ****************************************************************
         * 1  Čeká se na platbu šekem                       cheque
         * 2  Platba byla přijata                           payment
         * 3  Probíhá příprava                              preparation
         * 4  Odeslána                                      shipped
         * 5  Dodáno
         * 6  Zrušeno                                       order_canceled
         * 7  Splaceno                                      refund
         * 8  Chyba platby                                  payment_error
         * 9  U dodavatele (zaplaceno)                      outofstock
         * 10 Čeká se na přijetí bezhotovostní platby       bankwire
         * 11 Bezhotostní platba přijata                    payment
         * 12 U dodavatele (nezaplaceno)                    outofstock
         */

        $helper = new VarioHelper();

        switch ($statusId) {
            case 2:
            case 11:
                // Poslani objednavky do varia
                $helper->export_order($params['id_order'], $statusId);
                break;
            case 4:
                // Stazeni faktury
                $helper->download_invoice($params['id_order'], $statusId);
                break;
        }
    }

    public function hookActionOrderEdited( $params ){
        /** @var Order $order */
        $order = $params['order'];

        $helper = new VarioHelper();
        $helper->export_order($order->id, $order->current_state);
    }

    public function hookDisplayPDFInvoice( $params ){
        $invoice = $params['object'];
        $order = new Order($invoice->id_order);
        if ($order->module == 'ps_wirepayment') {

            $array = explode('/', $_SERVER['BASE']);
            $str = '';
            foreach ($array as $item) {
                if ($item == $array[count($array) - 1]){
                    break;
                }
                $str .= $item . '/';
            }

            $path = 'Location: ' . $str . 'modules/hell_vario/invoices/' . $order->reference . '.pdf';
            header($path);

            //header('Location: /modules/hell_vario/invoices/' . $order->reference . '.pdf');
        }
    }
}