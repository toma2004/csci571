<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: NguyenTran
 * Date: 7/12/2015
 * Time: 12:10 AM
 */

if (count($products_in_category) <= 0)
{
    echo '<p style="color: red">ERROR: retrieving products for given product category</p>';
}
else
{
    foreach ($products_in_category as $myproducts)
    {
        echo '<div class="image_block">';
        echo '<input type="image" src="'.base_url().$myproducts["product_image"].'" alt="'.$myproducts["product_name"].'" style="width: 200px; height: 200px;" name="product_name_clicked" value="'.$myproducts["product_id"].'">';
        if ($myproducts["isOnSale"] == 1)
        {
            echo '<span class="caption"><span style="color: red">SALE: '.$myproducts["percentage_discount"].' %OFF</span><br/>';
            echo 'Orig. price = $'.$myproducts["product_price"].'<br/>';
            echo '<span style="color: red">Discounted price = $'.$myproducts["discounted"].'</span></span></div>';
        }
        else
        {
            echo 'Orig. price = $'.$myproducts["product_price"].'</div>';
        }

    }
    echo '<br/><br/>';
    echo '<button type="button" onclick=div_transform("form1");>Home</button>';
}

?>