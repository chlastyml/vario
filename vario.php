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

include_once dirname(__FILE__).'/classes/SoapMe.php';
include_once dirname(__FILE__).'/classes/Helper.php';
include_once dirname(__FILE__).'/classes/VarioProduct.php';
include_once dirname(__FILE__).'/ajax/VarioHelper.php';

class Vario extends Module
{
    public function __construct()
    {
        $this->name = 'vario';
        $this->tab = 'export';
        $this->version = '0.4.5.1';
        $this->author = 'Hellit';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->controllers = array('vario');

        parent::__construct();

        $this->displayName = $this->l('Vario Export/Import');
        $this->description = $this->l('Connect to Altus Vario.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

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
            !$this->installModuleTab()
        ) {
            return false;
        }

        $sqlPA = 'ALTER TABLE ' . _DB_PREFIX_ . 'product_attribute ADD `id_vario` VARCHAR(255)';
        $sqlO = 'ALTER TABLE ' . _DB_PREFIX_ . 'orders ADD `id_vario` VARCHAR(255)';
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
            $tab->name[$lang['id_lang']] = 'Vario';
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
        if ($id_tab){
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        return true;
    }

    public function hookActionOrderStatusUpdate( $params ) {
        $newOrderStatus = $params['newOrderStatus'];

        $idOrder = $params['id_order'];

        $order = new Order($idOrder);

        $breakpoint = null;

        $helper = new VarioHelper(true);

        $helper->export_order($order);

        /*
        if ( $newOrderStatus->id == Configuration::get( 'PS_OS_HElLTWISTO_INVOICE_ACTIVATED' ) ) {
            $order      = new Order( $params['id_order'] );
            $db         = Db::getInstance();
            $row        = $db->getRow( 'SELECT `twisto_id` FROM ' . _DB_PREFIX_ . "orders WHERE `reference` = '" . $order->reference . "'" );
            if ( isset( $row['twisto_id'] ) && !empty( $row['twisto_id'] ) ) {
                $invoice_id = $row['twisto_id'];

                $invoice = new Twisto\Invoice( $this->twisto, $invoice_id );

                //activate invoice at twisto
                $invoice->activate();

                //get all invoice data from twisto
                $invoice->get();

                //save external twisto invoice pdf to local folder
                $local_pdf = fopen( dirname( __FILE__ ) . '/invoices/' . $order->reference . '.pdf', 'w+' );
                $curl = curl_init( $invoice->pdf_url );
                curl_setopt( $curl, CURLOPT_TIMEOUT, 60 );
                curl_setopt( $curl, CURLOPT_FILE, $local_pdf );
                curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, 1 );
                curl_setopt( $curl, CURLOPT_ENCODING, "" );
                curl_exec( $curl );
                curl_close( $curl );
                fclose( $local_pdf );

                //file_put_contents( dirname( __FILE__ ) . '/invoices/' . $order->reference . '.pdf', fopen( $invoice->pdf_url, 'r' ) );
            }
        }
        */
    }
}