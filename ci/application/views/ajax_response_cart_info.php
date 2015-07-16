<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: NguyenTran
 * Date: 7/12/2015
 * Time: 12:10 AM
 */

if (isset($shopping_cart))
{
    $counter = 0;
    $px_from_top = 100;
    echo '<span style="position: absolute; left: 550px;">Price</span>';
    echo '<span style="position: absolute; left: 680px;">Quantity</span><br/>';
    foreach ($shopping_cart as $cart_items)
    {
        if ($counter == 0)
        {
            echo '<div class="shopping_cart_item">';
        }
        else
        {
            $px_from_top += 120;
            echo '<div class="shopping_cart_item" style="position: absolute; top: '.$px_from_top.'px;">';
        }
        $counter += 1;
        echo '<div class="other_components">';
        echo '<img src="'.base_url().$cart_items["product_image"].'" height="100px" width="100px"></div>';

        echo '<div class="pname">';
        echo '<span>'.$cart_items["product_name"].'</span></div>';

        echo '<div class="other_components">';
        echo '<span style="position: absolute; left: 550px; color: red;">'.$cart_items["discounted"].'</span>';
        echo '<select style="position: absolute; left: 690px;" onchange=change_quality("'.$cart_items["pid"].'",this.selectedIndex);>';
        /*Set default selected based on product quantity in the cart*/
        echo '<option value="1">1</option>';
        if ($cart_items["qty"] == 2)
        {
            echo '<option value="2" selected="selected">2</option>';
        }
        else
        {
            echo '<option value="2">2</option>';
        }
        if ($cart_items["qty"] == 3)
        {
            echo '<option value="3" selected="selected">3</option>';
        }
        else
        {
            echo '<option value="3">3</option>';
        }
        if ($cart_items["qty"] == 4)
        {
            echo '<option value="4" selected="selected">4</option>';
        }
        else
        {
            echo '<option value="4">4</option>';
        }
        if ($cart_items["qty"] == 5)
        {
            echo '<option value="5" selected="selected">5</option></select></div>';
        }
        else
        {
            echo '<option value="5">5</option></select></div>';
        }
        echo '<button type="button" class="delete_id" onclick=delete_product_cart("'.$cart_items["pid"].'");>delete</button></div>';
    }
    if ($counter == 0)
    {
        echo '<p style="color: red; font-weight: bold;">Your shopping cart is empty</p>';
    }
    else
    {
        $px_from_top += 120;
        echo '<button type="button" style="position: absolute; top: '.$px_from_top.'px; left: 0;" onclick=delete_entire_cart();>Remove cart</button>';
    }
}
else if (isset($change_quantity_product_cart))
{
    echo $change_quantity_product_cart; //AJAX response to see if we have successfully changed product quantity
}
else if (isset($delete_product_cart))
{
    echo $delete_product_cart; //AJAX response to see if we have successfully removed a product in cart
}
else if (isset($delete_cart))
{
    echo $delete_cart; //AJAX response to see if we have successfully removed entire shopping cart
}
else
{
    echo '<p style="color: red; font-weight: bold;">Your shopping cart is empty</p>';
}
?>