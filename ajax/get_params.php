<?php

require_once('../../../config/config.inc.php');
require_once('../../../init.php');
require_once('VarioAjaxHelper.php');

if(Tools::getIsset('token') && Tools::getIsset('action'))
{
    $ajaxHelper = new VarioAjaxHelper();

    $result = $ajaxHelper->get_params();

    echo $result;
}
