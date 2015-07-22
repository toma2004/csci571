<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: NguyenTran
 * Date: 7/12/2015
 * Time: 12:10 AM
 */



?><!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8"/>
    <meta name="author" content="Nguyen Tran"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <?php echo link_tag('css/main_css.css'); ?> <!-- Include css style sheet from folder /css -->
                                <!--<link rel="shortcut icon" type="image/jpg" href="flower_crown1.jpg"></link> -->
    <title>Product info</title>
</head>
<body>
<form id="display_product_info" action="<?php echo base_url();?>index.php/main_webpage/response_product_detail_page" method="POST">
    <input type="hidden" name="hidden_add_to_cart_pid" value="<?php echo $detail_product_info['product_id']; ?>"/>
    <div class="product_detail_container">
        <div class="image_info">
            <div id="my_image">
                <img id="product_image" src="<?php echo base_url().$detail_product_info['product_image'];?>" alt="<?php echo htmlspecialchars($detail_product_info["product_name"]);?>">
            </div>
            <div id="sale_text">
            <?php
            if ($detail_product_info['isOnSale'] == 1 && isset($detail_product_info['start_date']) && isset($detail_product_info['end_date']) && isset($detail_product_info['percentage_discount']))
            {
                ?>
                <span id="sale_text_product_detail">This product <?php echo htmlspecialchars($detail_product_info["product_name"]);?> has special sale event discount at <br/><span style="color: red"><?php echo htmlspecialchars($detail_product_info["percentage_discount"]);?>% from <?php echo htmlspecialchars($detail_product_info["start_date"]);?> to <?php echo htmlspecialchars($detail_product_info["end_date"]);?></span></span><br/>
            <?php
            }
            ?>
                <span id="text_product_detail" style="padding-bottom: 1%;">ACT FAST and ORDER YOURS TODAY WHILE SUPPLIES LAST!</span><br/><br/>
                <button type="submit" name="add_to_cart" value="add_to_cart" id="add_to_cart_button">Add to Cart</button><br/>
            </div>
        </div>
        Product name:
        <span style="position:relative; left: 10%"><?php echo htmlspecialchars($detail_product_info["product_name"]);?></span><br/>
        <div id="p_desp" style="position: absolute;">
            <p class="display_product_info">
            Product Description:
            <textarea rows="10" cols="30" readonly style="position:relative; left: 10%"><?php echo htmlspecialchars($detail_product_info["product_description"]);?></textarea></p>

        </div>
        <div id="ingredient" style="position: relative; top: 200px;">
            <p class="display_product_info">
        Ingredient:
            <textarea rows="10" cols="30" readonly style="position: relative; left: 10.5%"><?php echo htmlspecialchars($detail_product_info["ingredients"]);?></textarea></p>

        </div>
    <div id="category" style="position: relative; top: 250px;">
        <p class="display_product_info">
        Belong to category:
            <textarea rows="10" cols="30" readonly style="position:relative; left: 5%"><?php echo htmlspecialchars($detail_product_info["category_name"]);?></textarea></p>
        </div>
        <button type="submit" name="to_home" value="to_home" style="position: relative; top: 250px;">Home</button>
    </div>
</form>
</body>
</html>