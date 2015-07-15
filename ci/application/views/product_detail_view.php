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

    <?php echo link_tag('css/main_css.css'); ?> <!-- Include css style sheet from folder /css -->
                                <!--<link rel="shortcut icon" type="image/jpg" href="flower_crown1.jpg"></link> -->
    <title>Product info</title>
</head>
<body>
<form id="display_product_info" action="<?php echo base_url();?>index.php/main_webpage/response_product_detail_page" method="POST">
    <input type="hidden" name="hidden_add_to_cart_pid" value="<?php echo $detail_product_info['product_id']; ?>"/>
    <img src="<?php echo base_url().$detail_product_info['product_image'];?>" alt="<?php echo htmlspecialchars($detail_product_info["product_name"]);?>" style="width: 400px; height: 200px;"><br/>
    Product name:
    <span style="position:absolute; left: 13%"><?php echo htmlspecialchars($detail_product_info["product_name"]);?></span><br/>
    <p class="display_product_info">
    Product Description:
        <textarea rows="20" cols="30" readonly style="position:relative; left: 5%"><?php echo htmlspecialchars($detail_product_info["product_description"]);?></textarea>
        <span style="position: absolute; left: 40%">Ingredient: </span>
        <textarea rows="20" cols="30" readonly style="position: absolute; left: 50%"><?php echo htmlspecialchars($detail_product_info["ingredients"]);?></textarea></p><br/>
    <p class="display_product_info">
    Belong to category:
        <textarea rows="20" cols="30" readonly style="position:relative; left: 5.5%"><?php echo htmlspecialchars($detail_product_info["category_name"]);?></textarea></p><br/>
    <?php
        if ($detail_product_info['isOnSale'] == 1 && isset($detail_product_info['start_date']) && isset($detail_product_info['end_date']) && isset($detail_product_info['percentage_discount']))
        {
    ?>
    This product <?php echo htmlspecialchars($detail_product_info["product_name"]);?> has special sale event discount at <span style="color: red"><?php echo htmlspecialchars($detail_product_info["percentage_discount"]);?>% from <?php echo htmlspecialchars($detail_product_info["start_date"]);?> to <?php echo htmlspecialchars($detail_product_info["end_date"]);?></span><br/>
    <?php
        }
    ?>
    ACT FAST and ORDER YOURS TODAY WHILE SUPPLIES LAST!<br/><br/>
    <button type="submit" name="to_home" value="to_home">Home</button>
    <button type="submit" name="add_to_cart" value="add_to_cart" style="position:relative; left:15px;">Add to Cart</button>
</form>
</body>
</html>