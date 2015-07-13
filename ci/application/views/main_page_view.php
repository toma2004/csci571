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
    <meta name="description" content="Food Catering"/>
    <meta name="keywords" content="HTML,CSS,XML,JavaScript"/>
    <meta name="author" content="Nguyen Tran"/>

    <?php echo link_tag('css/main_css.css'); ?> <!-- Include css style sheet from folder /css -->
    <!--<link rel="shortcut icon" type="image/jpg" href="flower_crown1.jpg"></link> -->
    <script src="<?php echo base_url(); ?>javascript/jquery-1.11.3.min.js"></script>
    <script src="<?php echo base_url(); ?>javascript/main_page_js.js"></script>
    <title>N2 Food</title>
</head>
<body>

<div class="container">
    <header>
        <h1 id="main_header">Welcome to N2 Food Catering</h1>
    </header>
    <nav id="nav_bar">
        <ul>
            <li class="first_tier"><a href="#">Home &#9662;</a></li>
            <li class="first_tier"><a href="#">Product Category &#9662;</a>
                <ul id="display_list_category_id">
                    <?php foreach ($category_list as $cl) {  ?>
                    <li onclick="display_category('<?php echo $cl['category_id']; ?>')"><span class="replace_a"><?php echo $cl['category_name'];?></span></li>
                    <?php } //End foreach loop for category list?>
                </ul>
            </li>
            <li class="first_tier"><a href="#">Log in/Sign up &#9662;</a>
                <ul>
                    <li><a href="#">Sign up</a></li>
                    <li><a href="#">Log in</a></li>
                </ul>
            </li>
            <li class="first_tier"><a href="#">Shopping Cart &#9662;</a>
                <ul>
                    <li onclick="request_shopping_cart_info()"><span class="replace_a">Edit your cart</span></li>
                    <li onclick="request_check_out()"><span class="replace_a">Proceed to checkout</span></li>
                </ul>
            </li>
            <li class="first_tier" onclick="request_contact()"><span class="replace_a">Contact Us &#9662;</span></li>
        </ul>
    </nav>
</div>

<div id="form1" style="position:absolute; top: 190px;">
    <h1 id="header_1" style="color: red">Special Sale Event!</h1>
    <p id="error_msg" style="color: red"></p>
    <form id="main_page_form" action="<?php echo base_url();?>index.php/main_webpage/get_product_detail" method="POST">
        <?php
        if (count($special_sale_display) == 0)
        {
        ?>
            <p style="color: red">Sorry, there is no product on sale right now</p>
        <?php
        }
        else
        {
            foreach ($special_sale_display as $ss_display) {

        ?>
        <div class="image_block">
            <input type="image" src="<?php echo base_url().$ss_display['product_image'];?>" alt="<?php echo $ss_display['product_name'];?>" style="width: 200px; height: 200px;" name="product_name_clicked" value="<?php echo $ss_display['product_id'];?>"/>
            <span class="caption">
                <span style="color: red">SALE: <?php echo $ss_display['percentage_discount'];?>%OFF</span><br/>
                Orig. price = <?php echo $ss_display['product_price'];?><br/>
                <span style="color: red">Discounted price = <?php $ss_display['discounted'];?></span>
            </span>
        </div>
        <?php } //end of foreach loop
        } //end else loop
        ?>

    </form>
</div>

<div id="display_category_div" style="position:absolute; top: 190px; display: none;">
    <h1 id="header_2" style="color: #eeeeee">Product Category</h1>
    <form id="display_category_form" action="#" method="POST">
    </form>
</div>

<div id="edit_shopping_cart_div" style="position:absolute; top: 190px; display: none;">
    <h1 id="header_3" style="color: #eeeeee">Your shopping cart:</h1>
    <form id="edit_shopping_cart_form" action="#" method="POST">
    </form>
</div>

<div id="checkout_summary_div" style="position:absolute; top: 190px; width: 90%; display: none;">
    <h1 id="header_4" style="color: #eeeeee">Review your order</h1>
    <form id="checkout_summary_form" action="#" method="POST">
    </form>
</div>

<div id="past_orders_summary_div" style="position:absolute; top: 190px; width: 90%; display: none;">
    <h1 id="header_5" style="color: #eeeeee">Your past orders:</h1>
    <form id="past_orders_summary_form" action="#" method="POST">
    </form>
</div>

<div id="past_order_detail_div" style="position:absolute; top: 190px; width: 90%; display: none;">
    <h1 id="header_6" style="color: #eeeeee">Review your order</h1>
    <form id="past_order_detail_form" action="#" method="POST">
    </form>
</div>

<div id="contact_us_div" style="position:absolute; top: 190px; width: 90%; display: none;">
    <h1 id="header_7" style="color: #eeeeee">Contact us:</h1>
    <form id="contact_us_form" action="#" method="POST">
    </form>
</div>

</body>
</html>