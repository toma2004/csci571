<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: NguyenTran
 * Date: 7/12/2015
 * Time: 12:10 AM
 */

?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8"/>
    <meta name="description" content="Food Catering"/>
    <meta name="keywords" content="HTML,CSS,XML,JavaScript"/>
    <meta name="author" content="Nguyen Tran"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/main_css.css"/> <!-- link to external css file -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/edit_profile_style.css"/> <!-- link to external css file -->
    <!--<link rel="shortcut icon" type="image/jpg" href="flower_crown1.jpg"></link> -->
    <script src="<?php echo base_url();?>javascript/jquery-1.11.3.min.js"></script>
    <script src="<?php echo base_url();?>javascript/main_page_js.js"></script>
    <title>Edit profile</title>
</head>
<body>

<div id="edit_profile">
    <h1>Hello <?php echo htmlspecialchars($customer_info["c_first_name"]).' '.htmlspecialchars($customer_info["c_last_name"]); ?>!</h1>
    <p>Please use the space below to edit your profile</p>
    <?php
    if (isset($customer_edit_profile))
    {
        ?>
        <p id="err_msg_edit_profile" style="color: red"><?php echo htmlspecialchars($customer_edit_profile);?></p>
    <?php
    }
    else
    {
        ?>
        <p id="err_msg_edit_profile" style="color: red"></p>
    <?php
    }//end of else statement
    ?>

    <div id="outer_form_box">
        <div class="outer_box_edit_element">
            <div class="first_box_element_edit_page"></div>
            <div class="second_box_element_edit_page" style="font-weight: bolder;font-size: 1.5em;">Current Value</div>
            <div id="text_change_value" style="font-weight: bold; font-size: 1.5em;">Change to value</div>
        </div>

        <form id="edit_profile_form" action="<?php echo base_url();?>index.php/main_webpage/edit_profile" method="POST">
            <input type="hidden" name="hidden_cus_id" value="<?php echo $_SESSION["cus_id"]; ?>"/>

            <?php
            #First and Last names
            echo '<div class="outer_box_edit_element">';
            echo '<div class="first_box_element_edit_page">Your first name:</div>';
            echo '<div class="second_box_element_edit_page">'.htmlspecialchars($customer_info["c_first_name"]).'</div>';
            echo '<input type="text" id="modified_first_name" name="modified_first_name" maxlength="30" pattern="[a-zA-Z]+\s*[a-zA-Z]*$"/>';
            echo '</div>';

            echo '<div class="outer_box_edit_element">';
            echo '<div class="first_box_element_edit_page">Your last name:</div>';
            echo '<div class="second_box_element_edit_page">'.htmlspecialchars($customer_info["c_last_name"]).'</div>';
            echo '<input type="text" id="modified_last_name" name="modified_last_name" maxlength="30" pattern="[a-zA-Z]+\s*[a-zA-Z]*$"/>';
            echo '</div>';

            #Shipping address
            echo '<div class="outer_box_edit_element">';
            echo '<div class="first_box_element_edit_page">Your shipping street address:</div>';
            echo '<div class="second_box_element_edit_page">'.htmlspecialchars($customer_info["c_street_addr_shipping"]).'</div>';
            echo '<input type="text" id="modified_street_addr_shipping" name="modified_street_addr_shipping" maxlength="50" pattern="([0-9])+\s+([A-Za-z])+\s*[a-zA-Z]*$"/>';
            echo '</div>';

            echo '<div class="outer_box_edit_element">';
            echo '<div class="first_box_element_edit_page">Your shipping city:</div>';
            echo '<div class="second_box_element_edit_page">'.htmlspecialchars($customer_info["c_city_shipping"]).'</div>';
            echo '<input type="text" id="modified_city_shipping" name="modified_city_shipping" maxlength="20" pattern="[a-zA-Z]+\s*[a-zA-Z]*$"/>';
            echo '</div>';

            echo '<div class="outer_box_edit_element">';
            echo '<div class="first_box_element_edit_page">Your shipping state:</div>';
            echo '<div class="second_box_element_edit_page">'.htmlspecialchars($customer_info["c_state_shipping"]).'</div>';
            echo '<input type="text" id="modified_state_shipping" name="modified_state_shipping" maxlength="2" size="2" pattern="[A-Za-z]{2}"/>';
            echo '</div>';

            echo '<div class="outer_box_edit_element">';
            echo '<div class="first_box_element_edit_page">Your shipping country:</div>';
            echo '<div class="second_box_element_edit_page">'.htmlspecialchars($customer_info["c_country_shipping"]).'</div>';
            echo '<input type="text" id="modified_country_shipping" name="modified_country_shipping" maxlength="50" pattern="[a-zA-Z]+\s*[a-zA-Z]*$"/>';
            echo '</div>';

            #Date of birth
            echo '<div class="outer_box_edit_element">';
            echo '<div class="first_box_element_edit_page">Your date of birth:</div>';
            echo '<div class="second_box_element_edit_page">'.htmlspecialchars($customer_info["c_dob"]).'</div>';
            echo '<input type="date" id="modified_dob" name="modified_dob"/>';
            echo '</div>';

            #Credit card information
            echo '<div class="outer_box_edit_element">';
            echo '<div class="first_box_element_edit_page">Your credit card number:</div>';
            echo '<div class="second_box_element_edit_page">'.htmlspecialchars($customer_info["c_credit_card"]).'</div>';
            echo '<input type="text" id="modified_credit_card" name="modified_credit_card" pattern ="[0-9]{16}" size="16" maxlength="16"/>';
            echo '</div>';

            echo '<div class="outer_box_edit_element">';
            echo '<div class="first_box_element_edit_page">Your credit card security code:</div>';
            echo '<div class="second_box_element_edit_page">'.htmlspecialchars($customer_info["c_security_code"]).'</div>';
            echo '<input type="text" id="modified_security_code" name="modified_security_code" pattern ="[0-9]{3}" size="3" maxlength="3"/>';
            echo '</div>';

            echo '<div class="outer_box_edit_element">';
            echo '<div class="first_box_element_edit_page">Your credit card expiration month:</div>';
            echo '<div class="second_box_element_edit_page">'.htmlspecialchars($customer_info["c_exp_month"]).'</div>';
            echo '<input type="text" id="modified_exp_month" name="modified_exp_month" pattern ="[0-9]{1,2}" size="2" maxlength="2"/>';
            echo '</div>';

            echo '<div class="outer_box_edit_element">';
            echo '<div class="first_box_element_edit_page">Your credit card expiration year:</div>';
            echo '<div class="second_box_element_edit_page">'.htmlspecialchars($customer_info["c_exp_year"]).'</div>';
            echo '<input type="text" id="modified_exp_year" name="modified_exp_year" pattern ="[0-9]{4}" size="4" maxlength="4"/>';
            echo '</div>';

            #Billing address
            echo '<div class="outer_box_edit_element">';
            echo '<div class="first_box_element_edit_page">Your billing street address:</div>';
            echo '<div class="second_box_element_edit_page">'.htmlspecialchars($customer_info["c_street_addr_billing"]).'</div>';
            echo '<input type="text" id="modified_street_addr_billing" name="modified_street_addr_billing" maxlength="50" pattern="([0-9])+\s+([A-Za-z])+\s*[a-zA-Z]*$"/>';
            echo '</div>';

            echo '<div class="outer_box_edit_element">';
            echo '<div class="first_box_element_edit_page">Your billing city:</div>';
            echo '<div class="second_box_element_edit_page">'.htmlspecialchars($customer_info["c_city_billing"]).'</div>';
            echo '<input type="text" id="modified_city_billing" name="modified_city_billing" maxlength="20" pattern="[a-zA-Z]+\s*[a-zA-Z]*$"/>';
            echo '</div>';

            echo '<div class="outer_box_edit_element">';
            echo '<div class="first_box_element_edit_page">Your billing state:</div>';
            echo '<div class="second_box_element_edit_page">'.htmlspecialchars($customer_info["c_state_billing"]).'</div>';
            echo '<input type="text" id="modified_state_billing" name="modified_state_billing" maxlength="2" size="2" pattern="[A-Za-z]{2}"/>';
            echo '</div>';

            echo '<div class="outer_box_edit_element">';
            echo '<div class="first_box_element_edit_page">Your billing country:</div>';
            echo '<div class="second_box_element_edit_page">'.htmlspecialchars($customer_info["c_country_billing"]).'</div>';
            echo '<input type="text" id="modified_country_billing" name="modified_country_billing" maxlength="50" pattern="[a-zA-Z]+\s*[a-zA-Z]*$"/>';
            echo '</div>';

            #Phone
            echo '<div class="outer_box_edit_element">';
            echo '<div class="first_box_element_edit_page">Your phone number:</div>';
            echo '<div class="second_box_element_edit_page">'.htmlspecialchars($customer_info["c_phone"]).'</div>';
            echo '<input type="tel" id="modified_phone" name="modified_phone" maxlength="10" size="10" pattern="[0-9]{10}"/>';
            echo '</div>';

            #Email
            echo '<div class="outer_box_edit_element">';
            echo '<div class="first_box_element_edit_page">Your email address:</div>';
            echo '<div class="second_box_element_edit_page">'.htmlspecialchars($customer_info["c_email"]).'</div>';
            echo '<input type="email" id="modified_email" name="modified_email" maxlength="40" pattern="[A-Za-z0-9]+@[A-Za-z0-9]+\.[a-z]{2,3}$"/>';
            echo '</div>';

            #Password
            echo '<div class="outer_box_edit_element">';
            echo '<div class="first_box_element_edit_page">Change your password:</div>';
            echo '<div class="second_box_element_edit_page"></div>';
            echo '<input type="password" id="modified_password" name="modified_password" maxlength="20"/>';
            echo '</div>';

            echo '<br/><br/>';
            #Buttons
            echo '<button type="submit" name="to_home" value="to_home">Home</button>';
            echo '<button type="submit" name="submit_edit_profile_form" value="submit_edit_profile_form" id="submit_edit_profile_form" style="position: relative; left: 10px">Submit</button>';
            ?>
        </form>
    </div>
</div>
</body>
</html>