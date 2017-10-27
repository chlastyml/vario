<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 9. 10. 2017
 * Time: 10:20
 */

include_once dirname(__FILE__).'/../../classes/SoapMe.php';

class AdminVarioController extends ModuleAdminController
{
    public function init()
    {
        parent::init();
        $this->bootstrap = true;
    }

    public function initContent()
    {
         parent::initContent();
         $this->setTemplate('vario.tpl');
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJquery();
        $jsFile = _MODULE_DIR_.'vario/views/js/vario.js?w=1';
        $this->addJS($jsFile);
    }
}