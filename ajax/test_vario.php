<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 16. 10. 2017
 * Time: 11:30
 */

require_once('../../../config/config.inc.php');
require_once('../../../init.php');
require_once('VarioHelper.php');

if(Tools::getIsset('token') && Tools::getIsset('action'))
{
    $ajaxHelper = new VarioHelper(true);
    $ajaxHelper->getClient();

    echo null;
}