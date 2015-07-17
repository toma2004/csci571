<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: NguyenTran
 * Date: 7/12/2015
 * Time: 12:10 AM
 */

if (isset($order_info))
{
    foreach ($order_info as $order)
    {
        echo 'Order date: <span style="color: #6666ff">'.$order["order_date"].'</span>, order total amount: <span style="color: #6666ff">$'.($order["order_total_amount"] + $order["order_shipping_cost"] + $order["order_total_tax"]).'</span>,...';
        echo '<button type="button" class="more_info_past_order" onclick=request_detail_past_order("'.$order["order_id"].'");>(more)</button><br/><br/>';
    }
}
else if (isset($no_order))
{
    echo '<p style="color: red">You have not made any order before.</p>';
}
else if (isset($err))
{
    echo '<p style="color: red">ERROR: retrieving your past orders. Please try again.</p>';
}
?>