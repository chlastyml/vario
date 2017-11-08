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

        /*
         * ID Nazev                             template
         ***********************************************
         * 1  Čeká se na platbu šekem           cheque
         * 2  Platba byla přijata               payment
         * 3  Probíhá příprava                  preparation
         * 4  Odeslána                          shipped
         * 5  Dodáno
         * 6  Zrušeno                           order_canceled
         * 7  Splaceno                          refund
         * 8  Chyba platby                      payment_error
         * 9  U dodavatele (zaplaceno)          outofstock
         * 10 Čeká se na přijetí bezhotovostní platby       bankwire
         * 11 Bezhotostní platba přijata        payment
         * 12 U dodavatele (nezaplaceno)        outofstock
         */

        $statusId = $order->current_state;

        if ($statusId == 2 OR $statusId == 11) {
            $ajaxHelper->export_order($order, $order->current_state);
            // TODO odstranit
            break;
        }
    }

    echo 'Export Complete';
}