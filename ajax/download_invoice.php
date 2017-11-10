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

    $varioHelper = new VarioHelper();

    foreach ($result as $orderRow) {
        $orderId = $orderRow['id_order'];

        $order = new Order($orderId);
        $statusId = $order->current_state;

        $varioHelper->download_invoice($orderId, $order->current_state);
    }

    echo 'Export Complete';
}