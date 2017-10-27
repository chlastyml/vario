<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 27. 10. 2017
 * Time: 10:47
 */

require_once('../../../config/config.inc.php');
require_once('../../../init.php');
require_once('VarioAjaxHelper.php');

if(Tools::getIsset('token') && Tools::getIsset('action'))
{
    $ajaxHelper = new VarioAjaxHelper();

    $result = $ajaxHelper->export_order();

    echo $result;
}