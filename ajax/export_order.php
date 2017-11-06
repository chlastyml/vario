<?php
/**
 * Created by PhpStorm.
 * User: kacal
 * Date: 27. 10. 2017
 * Time: 10:47
 */

require_once('../../../config/config.inc.php');
require_once('../../../init.php');
require_once('VarioHelper.php');

if(Tools::getIsset('token') && Tools::getIsset('action'))
{
    $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'orders`';
    $result = Db::getInstance()->executeS($sql);

    $ajaxHelper = new VarioHelper(true);

    foreach ($result as $orderRow) {
        $orderId = $orderRow['id_order'];

        $order = new Order($orderId);

        $ajaxHelper->export_order($order);

        // TODO odstranit
        break;
    }

    echo $result;
}