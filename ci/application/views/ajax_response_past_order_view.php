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
    echo '<div id="past_order_info">';
    foreach ($order_info as $order)
    {
        echo 'Order date: <span style="color: #6666ff">'.htmlspecialchars($order["order_date"]).'</span>, order total amount: <span style="color: #6666ff">$'.htmlspecialchars(($order["order_total_amount"] + $order["order_shipping_cost"] + $order["order_total_tax"])).'</span>,...';
        echo '<button type="button" class="more_info_past_order" onclick=request_detail_past_order("'.$order["order_id"].'");>(more)</button><br/><br/>';
    }
    echo '</div>';
}
else if (isset($no_order))
{
    echo '<p style="color: red">You have not made any order before.</p>';
}
else if (isset($err))
{
    echo '<p style="color: red">ERROR: retrieving your past orders. Please try again.</p>';
}
else if (isset($order_detail) && isset($order_detail_item) && isset($customer_info))
{
    echo '<div id="customer_info_order_summary">';
    echo '<div id="shipping_addr_past_order">';
    echo '<span>Shipping address</span><br/>';
    echo '<span>'.htmlspecialchars($customer_info["c_first_name"]).' '.htmlspecialchars($customer_info["c_last_name"]).'</span><br/>';
    echo '<span>'.htmlspecialchars($customer_info["c_street_addr_shipping"]).'</span><br/>';
    echo '<span>'.htmlspecialchars($customer_info["c_city_shipping"]).', '.htmlspecialchars($customer_info["c_state_shipping"]).'</span><br/>';
    echo '<span>'.htmlspecialchars($customer_info["c_country_shipping"]).'</span><br/></div>'; //End div=shipping_addr_past_order

    echo '<div id="payment_method_past_order">';
    echo '<span>Credit card ending in '.htmlspecialchars(substr($customer_info["c_credit_card"],-4)).'</span><br/></div>'; //End div=payment_method_past_order

    echo '<div id="order_summary_past_order">';
    echo '<span style="font-weight: bold; color: orange; position: relative;">Order summary</span><br/>';
    echo '<span>Item(s) Subtotal: </span>';
    echo '<span class="indent_left">$'.htmlspecialchars($order_detail["order_total_amount"]).'</span><br/>';
    echo '<span>Shipping & handling:</span>';
    echo '<span class="indent_left">$'.htmlspecialchars($order_detail["order_shipping_cost"]).'</span><br/>';
    echo '<span >Total before tax:</span>';
    echo '<span class="indent_left">$'.htmlspecialchars(($order_detail["order_total_amount"]+$order_detail["order_shipping_cost"])).'</span><br/>';
    echo '<span id="tax_to_be_collected">Estimated tax to be collected:</span>';
    echo '<span class="indent_left">'.htmlspecialchars($order_detail["order_total_tax"]).'</span><br/>';
    echo '<span style="font-weight: bold; color: red">Grand total:</span>';
    echo '<span class="indent_left" style="font-weight: bold; color: red">$'.htmlspecialchars(($order_detail["order_total_amount"]+$order_detail["order_shipping_cost"]+$order_detail["order_total_tax"])).'</span><br/>';
    echo '<span>Order date:</span>';
    echo '<span class="indent_left">'.htmlspecialchars($order_detail["order_date"]).'</span></div></div>'; //End div=order_summary_past_order and div=customer_info_order_summary

    $mydiv_height = $order_detail_item["total_products_in_order"] * 120 + 20;
    echo '<div id="products_info" style="height: '.$mydiv_height.'px;">';
    echo '<span id="price_tag">Price</span>';
    echo '<span id="qty_tag">Quantity</span><br/>';
    $item_div_height = 0;
    foreach ($order_detail_item['order_items'] as $row_order_item)
    {
        if($item_div_height == 0)
        {
            echo '<div class="shopping_cart_item_past_order">';
            $item_div_height += 120;
        }
        else
        {
            echo '<div class="shopping_cart_item_past_order" style="position: relative; top: '.$item_div_height.'px;">';
            $item_div_height += 120;
        }
        /*Need to get product info*/
        echo '<div class="img_product_cart">';
        echo '<img src="'.base_url().$row_order_item["product_image"].'" height="100px" width="100px"></div>';
        echo '<div class="pname_past_order">';
        echo '<span>'.htmlspecialchars($row_order_item["product_name"]).'</span></div>';
        echo '<div class="price_quantity_past_order">';
        echo '<span style="color: red">$'.htmlspecialchars($row_order_item["p_price"]).'</span>';
        echo '<span style="position: relative; float: right; right: 2.5%;">'.htmlspecialchars($row_order_item["order_quantity"]).'</span></div></div>'; //End div=price_quantity_past_order and div=shopping_cart_item_past_order
    }
    echo '<button type="button" style="position: absolute; top: '.($mydiv_height).'px;" onclick=div_transform("back_to_past_orders_summary_div");>Back</button></div>'; //End div=products_info
}
?>