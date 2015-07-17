<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: NguyenTran
 * Date: 7/12/2015
 * Time: 12:10 AM
 */

if (isset($customer_info_checkout) && isset($shopping_cart_info_checkout))
{
    echo '<div id="customer_shopping_cart_info">';
    echo '<div id="customer_info">';
    echo '<div id="shipping_addr">';
    echo '<span>Shipping address</span><br/>';
    echo '<span>'.$customer_info_checkout["c_first_name"].' '.$customer_info_checkout["c_last_name"].'</span><br/>';
    echo '<span>'.$customer_info_checkout["c_street_addr_shipping"].'</span><br/>';
    echo '<span>'.$customer_info_checkout["c_city_shipping"].', '.$customer_info_checkout["c_state_shipping"].'</span><br/>';
    echo '<span>'.$customer_info_checkout["c_country_shipping"].'</span><br/>';
    echo '<span>Phone: '.$customer_info_checkout["c_phone"].'</span></div>';

    echo '<div id="billing_addr">';
    echo '<span>Billing address</span><br/>';
    echo '<span>'.$customer_info_checkout["c_first_name"].' '.$customer_info_checkout["c_last_name"].'</span><br/>';
    echo '<span>'.$customer_info_checkout["c_street_addr_billing"].'</span><br/>';
    echo '<span>'.$customer_info_checkout["c_city_billing"].', '.$customer_info_checkout["c_state_billing"].'</span><br/>';
    echo '<span>'.$customer_info_checkout["c_country_billing"].'</span></div>';

    echo '<div id="payment_method">';
    echo '<span>Credit card ending in '.substr($customer_info_checkout["c_credit_card"],-4).'</span><br/>';
    echo '<button type="submit" id="edit_info" name="edit_profile_from_checkout" value="edit_profile_from_checkout">Edit info</button><br/></div></div>'; //End div customer_info

    #Start of div=shopping_cart_info
    /*Need to calculate total height for this div*/
    $elements = count($shopping_cart_info_checkout);
    $initial_height = 10 + ($elements * 120) + 40; /*First product will be 10px from top. Each of the following (including space for the "edit cart" button) will be += 120px from top. We will also add extra 40px at the end to create margin*/
    echo '<div id="shopping_cart_info" style="height: '.$initial_height.'px;">';
    echo '<span style="position: absolute; left: 70%; top: 1.5%;">Price</span>';
    echo '<span style="position: absolute; left: 77%; top: 1.5%;">Quantity</span><br/>';
    /*display each item in shopping cart*/
    $counter = 0;
    $myheight = 0;
    $total_amount = 0;
    foreach ($shopping_cart_info_checkout as $i => $cart_items)
    {
        //update total amount
        $total_amount += ((float)$cart_items["discounted"] * (float)$cart_items["qty"]);
        if ($counter == 0)
        {
            $myheight = 10;
            echo '<div class="shopping_cart_item" style="position: relative; top: 10px; left: 25px;">';
            $counter += 1;
        }
        else
        {
            $myheight += 120;
            echo '<div class="shopping_cart_item" style="position: relative; top: '.$myheight.'px; left: 25px;">';
        }
        echo '<div class="img_product_cart">';
        echo '<img src="'.base_url().$cart_items["product_image"].'" height="100px" width="100px"></div>';
        echo '<div class="pname_cart">';
        echo '<span>'.$cart_items["product_name"].'</span></div>';
        echo '<div class="price_quantity_cart">';
        echo '<span style="color: red">'.$cart_items["discounted"].'</span>';
        echo '<span style="position: absolute; left: 100%;">'.$cart_items["qty"].'</span></div></div>'; //End div=shopping_cart_item
    }
    $myheight += 120;
    echo '<button type="button" onclick="request_shopping_cart_info()" id="edit_cart_checkout" style="top: '.$myheight.'px;">Edit cart</button></div></div>'; //End div=shooping_cart_info and div=customer_shopping_cart_info

    //Start div=order_summary
    echo '<div id="order_summary">';
    echo '<button type="submit" id="place_order_checkout" name="place_order" value="place_order">Place your order</button><br/>';
    echo '<span style="font-weight: bold; color: orange; position: relative; left: 5%; top: 5%">Order summary</span><br/>';
    echo '<span style="position: relative; left: 5%; top: 5%">Item ('.count($shopping_cart_info_checkout).'):</span>';
    $total_amount = number_format($total_amount, 2, '.', ',');
    echo '<span class="indent_left">$'.$total_amount.'</span><br/>';
    echo '<span style="position: relative; left: 5%; top: 5%">Shipping & handling:</span>';
    echo '<span class="indent_left">$5.99</span><br/>';
    echo '<span style="position: relative; left: 5%; top: 5%">Total before tax:</span>';
    $total_and_shipping = $total_amount + 5.99;
    echo '<span class="indent_left">$'.$total_and_shipping.'</span><br/>';
    echo '<span style="position: relative; left: 5%; top: 5%">Estimated tax to be collected:</span>';
    $tax = 0.0875 * $total_amount;
    $tax = number_format($tax, 2, '.', ',');
    echo '<span class="indent_left">$'.$tax.'</span><br/>';
    echo '<span style="font-weight: bold; color: red; position: relative; left: 5%; top: 5%">Order total:</span>';
    $grand_total = $tax + $total_and_shipping;
    $grand_total = number_format($grand_total, 2, '.', ',');
    echo '<span class="indent_left" style="font-weight: bold; color: red">$'.$grand_total.'</span></div>'; //End div=order_summary
    echo '<input type="hidden" name="hidden_order_total_amount" value="'.$total_amount.'"/>';
    echo '<input type="hidden" name="hidden_order_total_tax" value="'.$tax.'"/>';
    echo '<input type="hidden" name="hidden_order_total_shipping" value="5.99"/>';
}
else if (isset($need_log_in_before_checkout))
{
    /*Display log in prompt for customer to log in*/
    echo '<div  class="outer_box_login">';
    echo '<div class="login_form1">';
    echo '<p id="error_message_log_in_page" style="color: red">Please log in before proceeding to check out</p>';
    echo '<label for="usrname">User Name</label><span style="color: red">*</span>';
    echo '<input type="text" id="usrname" name="user_name" maxlength="30" pattern="[a-zA-z0-9]+" required/><br/>';
    echo '<label for="pwd">Password</label><span style="color: red">*</span>';
    echo '<input type="password" id="pwd" name="pass_word" maxlength="30" required style="position:relative; left:11px;"/><br/><br/>';
    echo '<button type="button" onclick=div_transform("form1");>Home</button>';
    echo '<button type="submit" onclick="validate_log_in_page()" name="submit_log_in" value="submit_log_in" style="position: relative; left: 10px">Submit</button></div></div>'; //End div=outer_box_login and div=login_form1
}
else if (isset($cart_empty_checkout))
{
    echo '<p style="color: red">Your shopping cart is empty. Please add a product to your cart before check out</p>';
}
else
{
    echo '<p style="color: red; font-weight: bold;">ERROR: generating your order summary</p>';
}
?>