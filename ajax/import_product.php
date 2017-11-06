<?php

$LOG_DIR_PATH = dirname(__FILE__) . '/../logs';
$LOG_PATH = $LOG_DIR_PATH . '/log.txt';

require_once('../../../config/config.inc.php');
require_once('../../../init.php');
require_once('VarioHelper.php');
require_once('../classes/ImportProduct.php');

if(Tools::getIsset('token') && Tools::getIsset('action'))
{
    try {
        $ajaxHelper = new VarioHelper(true);

        $result = $ajaxHelper->import_product();

        echo $result;
    }catch (Exception $exception){
        echo $exception->getMessage();
    }
}