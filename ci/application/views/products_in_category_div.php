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
        echo '<input type="image" class="my_image" src="'.base_url().$myproducts["product_image"].'" alt="'.htmlspecialchars($myproducts["product_name"]).'" name="product_name_clicked" value="'.$myproducts["product_id"].'">';
        if ($myproducts["isOnSale"] == 1)
        {
            echo '<span class="caption"><span class="special_sale_ad">SALE: '.htmlspecialchars($myproducts["percentage_discount"]).' %OFF</span><br/>';
            echo '<span class="orig_ad">Orig. price = $'.htmlspecialchars($myproducts["product_price"]).'</span><br/>';
            echo '<span class="special_sale_ad">Discounted price = $'.htmlspecialchars($myproducts["discounted"]).'</span></span>';
        }
        else
        {
            echo '<span class="caption">';
            echo '<span class="orig_ad">Orig. price = $'.htmlspecialchars($myproducts["product_price"]).'</span><br/>';
            echo '</span>';
        }
        echo '</div>';

    }
}

?>