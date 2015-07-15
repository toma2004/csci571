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

    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/main_css.css"/> <!-- link to external css file -->
    <!--<link rel="shortcut icon" type="image/jpg" href="flower_crown1.jpg"></link> -->
    <script src="<?php echo base_url();?>javascript/jquery-1.11.3.min.js"></script>
    <script src="<?php echo base_url();?>javascript/main_page_js.js"></script>
    <title>N2 customer sign-up</title>
</head>
<body>

<div id="sign_up_form">
    <h1 id="header_1">Sign up</h1>
    <p id="error_msg_sign_up_form" style="color: red">
        <?php
        if (isset($from_signup))
        {
            echo "ERROR: Inserting new customer";
        }

        ?>
    </p>
    <form id="main_page_sign_up_form" action="<?php echo base_url();?>index.php/main_webpage/user_sign_up" method="POST">
        <!-- FIRST NAME, LAST NAME-->
        <label for="fname">First Name</label><span class="asterisk_red">*</span>
        <label for="lname" style="position:relative; left: 100px;">Last Name</label><span style="position:relative; left: 100px;" class="asterisk_red">*</span><br/>

        <input type="text" id="fname" name="first_name" maxlength="30" pattern="[a-zA-Z]+\s*[a-zA-Z]*$" required/>
        <input type="text" id="lname" name="last_name" maxlength="30" pattern="[a-zA-Z]+\s*[a-zA-Z]*$" required/> <br/><br/>

        <!--SHIPPING ADDRESS INFO-->
        <p>Please enter your shipping address</p>
        <label for="myaddr_shipping">Street address</label><span class="asterisk_red">*</span>
        <label for="mycity_shipping" style="position:relative; left: 80px;">City</label><span style="position:relative; left: 80px;" class="asterisk_red">*</span>
        <label for="mystate_shipping" style="position:relative; left: 210px;">State</label><span style="position:relative; left: 210px;" class="asterisk_red">*</span>
        <label for="mycountry_shipping" style="position:relative; left: 230px;">Country</label><span style="position:relative; left: 230px;" class="asterisk_red">*</span><br>

        <input type="text" id="myaddr_shipping" name="addr_shipping" maxlength="50" pattern="([0-9])+\s+([A-Za-z])+\s*[a-zA-Z]*$" required/>
        <input type="text" id="mycity_shipping" name="city_shipping" maxlength="20" pattern="[a-zA-Z]+\s*[a-zA-Z]*$" required/>
        <input type="text" id="mystate_shipping" name="state_shipping" maxlength="2" size="2" pattern="[A-Za-z]{2}" required/>
        <input type="text" id="mycountry_shipping" name="country_shipping" maxlength="50" pattern="[a-zA-Z]+\s*[a-zA-Z]*$" required/><br><br>

        <!-- DOB -->
        <label for="mydob">Date of birth</label><span class="asterisk_red">*</span><br>
        <input type="date" id="mydob" name="dob" required/><br><br>

        <!-- Credit card number & Security Code -->
        <label for="mycreditcard">Credit card number</label><span class="asterisk_red">*</span>
        <label for="mysecuritycode" style="position:relative; left: 80px;">Security Code</label><span style="position:relative; left: 80px;" class="asterisk_red">*</span><br>

        <input type="text" id="mycreditcard" name="mycreditcard" pattern ="[0-9]{16}" size="16" maxlength="16" required/>
        <input type="text" id="mysecuritycode" name="mysecuritycode" pattern ="[0-9]{3}" size="3" maxlength="3" style="position:relative; left: 80px;" required/><br><br>

        <!-- Expiration date -->
        <label for="myexpiredate_month">Expiration date month</label><span class="asterisk_red">*</span>
        <label for="myexpiredate_year" style="position:relative; left: 57px;">Expiration date year</label><span style="position:relative; left: 57px;" class="asterisk_red">*</span><br>

        <input type="text" id="myexpiredate_month" name="myexpiredate_month" pattern ="[0-9]{1,2}" size="2" maxlength="2" required/>
        <input type="text" id="myexpiredate_year" name="myexpiredate_year" pattern ="[0-9]{4}" size="4" maxlength="4" style="position:relative; left: 175px;" required/><br><br>

        <!-- BILLING ADDRESS INFO-->
        <p>Please enter your billing address</p>
        <label for="myaddr_billing">Street address</label><span class="asterisk_red">*</span>
        <label for="mycity_billing" style="position:relative; left: 80px;">City</label><span style="position:relative; left: 80px;" class="asterisk_red">*</span>
        <label for="mystate_billing" style="position:relative; left: 210px;">State</label><span style="position:relative; left: 210px;" class="asterisk_red">*</span>
        <label for="mycountry_billing" style="position:relative; left: 230px;">Country</label><span style="position:relative; left: 230px;" class="asterisk_red">*</span><br>

        <input type="text" id="myaddr_billing" name="addr_billing" maxlength="50" pattern="([0-9])+\s+([A-Za-z])+\s*[a-zA-Z]*$" required/>
        <input type="text" id="mycity_billing" name="city_billing" maxlength="20" pattern="[a-zA-Z]+\s*[a-zA-Z]*$" required/>
        <input type="text" id="mystate_billing" name="state_billing" maxlength="2" size="2" pattern="[A-Za-z]{2}" required/>
        <input type="text" id="mycountry_billing" name="country_billing" maxlength="50" pattern="[a-zA-Z]+\s*[a-zA-Z]*$" required/><br><br>

        <!--PHONE AND EMAIL -->
        <label for="myphone">Phone number (only numbers please)</label><span class="asterisk_red">*</span>
        <label for="myemail" style="position:relative; left: 40px;">Email</label><span style="position:relative; left: 40px;" class="asterisk_red">*</span><br>

        <input type="tel" id="myphone" name="phone" maxlength="10" size="10" pattern="[0-9]{10}" required/>
        <input type="email" id="myemail" name="email_addr" maxlength="40" style="position:relative; left: 210px;" pattern="[A-Za-z0-9]+@[A-Za-z0-9]+\.[a-z]{2,3}$" required/><br><br>

        <!-- USER NAME AND PASSWORD -->
        <label for="username">User name</label><span class="asterisk_red">*</span>
        <label for="pwd" style="position:relative; left: 100px;">Password</label><span style="position:relative; left: 100px;" class="asterisk_red">*</span><br>

        <input type="text" id="username" name="usr" maxlength="40" pattern="[a-zA-Z0-9]+" required />
        <input type="password" id="pwd" name="pass" maxlength="20" required /> <br/><br/>


        <a href="<?php echo base_url();?>index.php/main_webpage"><button type="button">Home</button></a>
        <button type="submit" id="submit_sign_up_form" name="submit_sign_up_form" value="submit_sign_up_form">Submit</button>

    </form>

</div>


</body>
</html>