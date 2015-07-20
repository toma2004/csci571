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
            <img id="product_image" src="<?php echo base_url().$detail_product_info['product_image'];?>" alt="<?php echo htmlspecialchars($detail_product_info["product_name"]);?>" style="width: 400px; height: auto;">
            <?php
            if ($detail_product_info['isOnSale'] == 1 && isset($detail_product_info['start_date']) && isset($detail_product_info['end_date']) && isset($detail_product_info['percentage_discount']))
            {
                ?>
                <span id="sale_text_product_detail">This product <?php echo htmlspecialchars($detail_product_info["product_name"]);?> has special sale event discount at <span style="color: red"><?php echo htmlspecialchars($detail_product_info["percentage_discount"]);?>% from <?php echo htmlspecialchars($detail_product_info["start_date"]);?> to <?php echo htmlspecialchars($detail_product_info["end_date"]);?></span></span>
            <?php
            }
            ?>
            <span id="sale_text_product_detail" style="padding-bottom: 1%;">ACT FAST and ORDER YOURS TODAY WHILE SUPPLIES LAST!</span>
            <button type="submit" name="add_to_cart" value="add_to_cart" id="add_to_cart_button">Add to Cart</button><br/>
        </div>
        Product name:
        <span style="position:absolute; left: 20%"><?php echo htmlspecialchars($detail_product_info["product_name"]);?></span><br/>
        <p class="display_product_info">
        Product Description:
            <textarea rows="20" cols="30" readonly style="position:relative; left: 5%"><?php echo htmlspecialchars($detail_product_info["product_description"]);?></textarea>
            <span style="position: absolute; left: 40%">Ingredient: </span>
            <textarea rows="20" cols="30" readonly style="position: absolute; left: 50%"><?php echo htmlspecialchars($detail_product_info["ingredients"]);?></textarea></p><br/>
        <p class="display_product_info">
        Belong to category:
            <textarea rows="20" cols="30" readonly style="position:relative; left: 5.5%"><?php echo htmlspecialchars($detail_product_info["category_name"]);?></textarea></p>

        <button type="submit" name="to_home" value="to_home">Home</button>
    </div>
</form>
</body>
</html>