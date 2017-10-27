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

        $this->displayName = $this->l('Vario module');
        $this->description = $this->l('Connect to vario.');

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
            //$this->registerHook('NameOfHook') ||
            !$this->registerHook('leftColumn') ||
            !$this->registerHook('header') ||
            !Configuration::updateValue('vario', 'my friend') ||
            !$this->installModuleTab()
        ) {
            return false;
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
}