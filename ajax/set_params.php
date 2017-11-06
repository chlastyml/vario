<?php

require_once('../../../config/config.inc.php');
require_once('../../../init.php');
require_once('VarioHelper.php');

if(Tools::getIsset('token') && Tools::getIsset('action'))
{
    $ajaxHelper = new VarioHelper();

    $ajaxHelper->set_params($_POST);

    echo 'DONE';
}
