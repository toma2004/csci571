<?php
/**
 * User: NguyenTran
 * Date: 7/1/2015
 * Time: 11:52 PM
 */

/*Establish my session array*/
if(!isset($_SESSION))
{
    session_start();
}

/*Check if customer log in*/
if (!isset($_SESSION['last_activity']) || !isset($_SESSION['username']) || !isset($_SESSION['password']) || !isset($_SESSION['cus_id']) || !isset($_SESSION['timeout']))
{
    require "main_webpage.html";
}
else
{
    $t = time();
    if (($t - $_SESSION['last_activity']) > 1800)
    {
        $_SESSION['timeout'] = 1;
        require "customer_logout.php";
    }
    else
    {
        #session is not yet timeout. Reset time to give customer another 30 mins
        $_SESSION['last_activity'] = time();

        if (isset($_POST["submit_edit_profile_form"])) /*User has submit a profile change*/
        {
            #Re-check to see if all fields are empty
            if ($_POST["modified_first_name"] != '' || $_POST["modified_last_name"] != '' || $_POST["modified_street_addr_shipping"] != '' || $_POST["modified_city_shipping"] != '' || $_POST["modified_state_shipping"] != '' || $_POST["modified_country_shipping"] != '' || $_POST["modified_dob"] != '' || $_POST["modified_credit_card"] != '' || $_POST["modified_security_code"] != '' || $_POST["modified_exp_month"] != '' || $_POST["modified_exp_year"] != '' || $_POST["modified_street_addr_billing"] != '' || $_POST["modified_city_billing"] != '' || $_POST["modified_state_billing"] != '' || $_POST["modified_country_billing"] != '' || $_POST["modified_phone"] != '' || $_POST["modified_password"] != '')
            {
                modify_profile();
            }
            else
            {
                require "pre_sign_up_page.html";
                echo "You have not made any changes";
                require "post_sign_up_page.html";
            }
        }
        else /*display edit profile page as it's supposed to*/
        {
            display_edit_profile_page();
        }
    }
}

/*Function to connect to DB*/
function connectDB()
{
    $host = 'localhost';
    $user = 'root';
    $pass = 'ntcsci571hw2';

    $conn = mysql_connect($host, $user, $pass);
    if (!$conn)
    {
        die("Could not connect to database");
    }
    mysql_select_db('n2_internal_db',$conn);
    return $conn;
}

/*Function to disconnect from db*/
function disconnectDB($myconn)
{
    mysql_close($myconn);
}


/*Function to display edit profile page*/
function display_edit_profile_page()
{
    $conn = connectDB();
    $sql = "select * from customers where customer_id='".$_SESSION["cus_id"]."'";
    $res = mysql_query($sql);
    if (!$res)
    {
        require "pre_main_webpage_logged_in.html";
        echo '<p style="color:red">ERROR: retrieving info for customer '.$_SESSION["username"].'</p>';
        require 'post_main_webpage_logged_in.html';
    }
    else
    {
        if (! ($row = mysql_fetch_assoc($res)))
        {
            #This customer is not in our database
            require "pre_main_webpage_logged_in.html";
            echo '<p style="color:red">ERROR: retrieving info for customer '.$_SESSION["username"].'</p>';
            require 'post_main_webpage_logged_in.html';
        }
        else
        {
            #Start to display info
            ?>
            <!DOCTYPE html>
            <html>
            <head lang="en">
                <meta charset="UTF-8"/>
                <meta name="description" content="Food Catering"/>
                <meta name="keywords" content="HTML,CSS,XML,JavaScript"/>
                <meta name="author" content="Nguyen Tran"/>

                <link rel="stylesheet" type="text/css" href="main_css.css"/> <!-- link to external css file -->
                <!--<link rel="shortcut icon" type="image/jpg" href="flower_crown1.jpg"></link> -->
                <script src="jquery-1.11.3.min.js"></script>
                <script src="main_page_js.js"></script>
                <title>Edit profile</title>
            </head>
            <body>
            <div id="edit_profile">
                <h1>Hello <?php echo $_SESSION["username"]; ?>!</h1>
                <p>Please use the space below to edit your profile</p>
                <p id="err_msg_edit_profile" style="color: red"></p>

                <span style="font-weight: bold;position:absolute; left: 15%">Current Value</span>
                <span style="font-weight: bold; position:absolute; left: 30%">Change to value</span><br/><br/>

                <form id="edit_profile_form" action="edit_profile.php" method="POST">
                    <input type="hidden" name="hidden_cus_id" value="<?php echo $_SESSION["cus_id"]; ?>"/>

                    <?php
                    #First and Last names
                    echo 'Your first name: ';
                    echo '<span style="position:absolute; left: 15%">'.$row["c_first_name"].'</span>';
                    echo '<input type="text" id="modified_first_name" name="modified_first_name" maxlength="30" pattern="[a-zA-z]+" style="position:absolute; left: 30%"/><br/><br/>';

                    echo 'Your last name: ';
                    echo '<span style="position:absolute; left: 15%">'.$row["c_last_name"].'</span>';
                    echo '<input type="text" id="modified_last_name" name="modified_last_name" maxlength="30" pattern="[a-zA-Z]+" style="position:absolute; left: 30%"/><br/><br/>';

                    #Shipping address
                    echo 'Your shipping street address: ';
                    echo '<span style="position:absolute; left: 15%">'.$row["c_street_addr_shipping"].'</span>';
                    echo '<input type="text" id="modified_street_addr_shipping" name="modified_street_addr_shipping" maxlength="50" pattern="([0-9])+\s+([A-Za-z])+.*" style="position:absolute; left: 30%"/><br/><br/>';

                    echo 'Your shipping city: ';
                    echo '<span style="position:absolute; left: 15%">'.$row["c_city_shipping"].'</span>';
                    echo '<input type="text" id="modified_city_shipping" name="modified_city_shipping" maxlength="20" pattern="\D+" style="position:absolute; left: 30%"/><br/><br/>';

                    echo 'Your shipping state: ';
                    echo '<span style="position:absolute; left: 15%">'.$row["c_state_shipping"].'</span>';
                    echo '<input type="text" id="modified_state_shipping" name="modified_state_shipping" maxlength="2" size="2" pattern="[A-Za-z]{2}" style="position:absolute; left: 30%"/><br/><br/>';

                    echo 'Your shipping country: ';
                    echo '<span style="position:absolute; left: 15%">'.$row["c_country_shipping"].'</span>';
                    echo '<input type="text" id="modified_country_shipping" name="modified_country_shipping" maxlength="50" pattern="\D+" style="position:absolute; left: 30%"/><br/><br/>';

                    #Date of birth
                    echo 'Your date of birth: ';
                    echo '<span style="position:absolute; left: 15%">'.$row["c_dob"].'</span>';
                    echo '<input type="date" id="modified_dob" name="modified_dob" style="position:absolute; left: 30%"/><br/><br/>';

                    #Credit card information
                    echo 'Your credit card number: ';
                    echo '<span style="position:absolute; left: 15%">'.$row["c_credit_card"].'</span>';
                    echo '<input type="text" id="modified_credit_card" name="modified_credit_card" pattern ="[0-9]{16}" size="16" maxlength="16" style="position:absolute; left: 30%"/><br/><br/>';

                    echo 'Your credit card security code: ';
                    echo '<span style="position:absolute; left: 15%">'.$row["c_security_code"].'</span>';
                    echo '<input type="text" id="modified_security_code" name="modified_security_code" pattern ="[0-9]{3}" size="3" maxlength="3" style="position:absolute; left: 30%"/><br/><br/>';

                    echo 'Your credit card expiration month: ';
                    echo '<span style="position:absolute; left: 15%">'.$row["c_exp_month"].'</span>';
                    echo '<input type="text" id="modified_exp_month" name="modified_exp_month" pattern ="[0-9]{1,2}" size="2" maxlength="2" style="position:absolute; left: 30%"/><br/><br/>';

                    echo 'Your credit card expiration year: ';
                    echo '<span style="position:absolute; left: 15%">'.$row["c_exp_year"].'</span>';
                    echo '<input type="text" id="modified_exp_year" name="modified_exp_year" pattern ="[0-9]{4}" size="4" maxlength="4" style="position:absolute; left: 30%"/><br/><br/>';

                    #Billing address
                    echo 'Your billing street address: ';
                    echo '<span style="position:absolute; left: 15%">'.$row["c_street_addr_billing"].'</span>';
                    echo '<input type="text" id="modified_street_addr_billing" name="modified_street_addr_billing" maxlength="50" pattern="([0-9])+\s+([A-Za-z])+.*" style="position:absolute; left: 30%"/><br/><br/>';

                    echo 'Your billing city: ';
                    echo '<span style="position:absolute; left: 15%">'.$row["c_city_billing"].'</span>';
                    echo '<input type="text" id="modified_city_billing" name="modified_city_billing" maxlength="20" pattern="\D+" style="position:absolute; left: 30%"/><br/><br/>';

                    echo 'Your billing state: ';
                    echo '<span style="position:absolute; left: 15%">'.$row["c_state_billing"].'</span>';
                    echo '<input type="text" id="modified_state_billing" name="modified_state_billing" maxlength="2" size="2" pattern="[A-Za-z]{2}" style="position:absolute; left: 30%"/><br/><br/>';

                    echo 'Your billing country: ';
                    echo '<span style="position:absolute; left: 15%">'.$row["c_country_billing"].'</span>';
                    echo '<input type="text" id="modified_country_billing" name="modified_country_billing" maxlength="50" pattern="\D+" style="position:absolute; left: 30%"/><br/><br/>';

                    #Phone
                    echo 'Your phone number: ';
                    echo '<span style="position:absolute; left: 15%">'.$row["c_phone"].'</span>';
                    echo '<input type="tel" id="modified_phone" name="modified_phone" maxlength="10" size="10" pattern="[0-9]{10}" style="position:absolute; left: 30%"/><br/><br/>';

                    #Password
                    echo 'Change your password: ';
                    echo '<input type="password" id="modified_password" name="modified_password" maxlength="20" style="position:absolute; left: 30%"/><br/><br/><br/>';

                    echo '<a href="main_page.php"><button type="button">Home</button></a>';
                    echo '<button type="submit" name="submit_edit_profile_form" id="submit_edit_profile_form" style="position: relative; left: 10px">Submit</button>';
                    ?>
                </form>
            </div>
            </body>
            </html>

        <?php
        }
    }
    disconnectDB($conn);
}

/*Function to modify profile*/
function modify_profile()
{
    $conn = connectDB();
    $cus_id = $_POST["hidden_cus_id"];
    $err_msg = "";
    if ($_POST["modified_first_name"] != '')
    {
        /*Need to validate data before making any changes*/
        $fname = validate_data($_POST["modified_first_name"],"first_name");
        if ($fname == false)
        {
            $err_msg .= "First name is not in a right format\r\n";
        }
        else
        {
            $sql = "update customers set c_first_name='".$fname."' where customer_id='".$cus_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                $err_msg .= "ERROR: updating first name of customer's id ".$cus_id."\r\n";
            }
        }
    }
    if ($_POST["modified_last_name"] != '')
    {
        /*Need to validate data before making any changes*/
        $lname = validate_data($_POST["modified_last_name"],"last_name");
        if ($lname == false)
        {
            $err_msg .= "Last name is not in a right format\r\n";
        }
        else
        {
            $sql = "update customers set c_last_name='".$lname."' where customer_id='".$cus_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                $err_msg .= "ERROR: updating last name of customer's id ".$cus_id."\r\n";
            }
        }
    }
    if ($_POST["modified_street_addr_shipping"] != '')
    {
        /*Need to validate data before making any changes*/
        $addr_shipping = validate_data($_POST["modified_street_addr_shipping"],"address");
        if ($addr_shipping == false)
        {
            $err_msg .= "Shipping street address is not in a right format\r\n";
        }
        else
        {
            $sql = "update customers set c_street_addr_shipping='".$addr_shipping."' where customer_id='".$cus_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                $err_msg .= "ERROR: updating Shipping street address of customer's id ".$cus_id."\r\n";
            }
        }
    }
    if ($_POST["modified_city_shipping"] != '')
    {
        /*Need to validate data before making any changes*/
        $city_shipping = validate_data($_POST["modified_city_shipping"],"city");
        if ($city_shipping == false)
        {
            $err_msg .= "Shipping city is not in a right format\r\n";
        }
        else
        {
            $sql = "update customers set c_city_shipping='".$city_shipping."' where customer_id='".$cus_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                $err_msg .= "ERROR: updating shipping city of customer's id ".$cus_id."\r\n";
            }
        }
    }
    if ($_POST["modified_state_shipping"] != '')
    {
        /*Need to validate data before making any changes*/
        $state_shipping = validate_data($_POST["modified_state_shipping"],"state");
        if ($state_shipping == false)
        {
            $err_msg .= "Shipping state is not in a right format\r\n";
        }
        else
        {
            $sql = "update customers set c_state_shipping='".$state_shipping."' where customer_id='".$cus_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                $err_msg .= "ERROR: updating shipping state of customer's id ".$cus_id."\r\n";
            }
        }
    }
    if ($_POST["modified_country_shipping"] != '')
    {
        /*Need to validate data before making any changes*/
        $country_shipping = validate_data($_POST["modified_country_shipping"],"country");
        if ($country_shipping == false)
        {
            $err_msg .= "Shipping country is not in a right format\r\n";
        }
        else
        {
            $sql = "update customers set c_country_shipping='".$country_shipping."' where customer_id='".$cus_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                $err_msg .= "ERROR: updating shipping country of customer's id ".$cus_id."\r\n";
            }
        }
    }
    if ($_POST["modified_dob"] != '')
    {
        /*Need to validate data before making any changes*/
        $dob = validate_data($_POST["modified_dob"],"dob");
        if ($dob == false)
        {
            $err_msg .= "Date of birth is not in a right format\r\n";
        }
        else
        {
            $sql = "update customers set c_dob='".$dob."' where customer_id='".$cus_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                $err_msg .= "ERROR: updating date of birth of customer's id ".$cus_id."\r\n";
            }
        }
    }
    if ($_POST["modified_credit_card"] != '')
    {
        /*Need to validate data before making any changes*/
        $credit_card = validate_data($_POST["modified_credit_card"],"credit_card");
        if ($credit_card == false)
        {
            $err_msg .= "Credit card number is not in a right format\r\n";
        }
        else
        {
            $sql = "update customers set c_credit_card='".$credit_card."' where customer_id='".$cus_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                $err_msg .= "ERROR: updating credit card number of customer's id ".$cus_id."\r\n";
            }
        }
    }
    if ($_POST["modified_security_code"] != '')
    {
        /*Need to validate data before making any changes*/
        $security_code = validate_data($_POST["modified_security_code"],"security_code");
        if ($security_code == false)
        {
            $err_msg .= "Security code is not in a right format\r\n";
        }
        else
        {
            $sql = "update customers set c_security_code='".$security_code."' where customer_id='".$cus_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                $err_msg .= "ERROR: updating security code of customer's id ".$cus_id."\r\n";
            }
        }
    }
    if ($_POST["modified_exp_month"] != '')
    {
        /*Need to validate data before making any changes*/
        $exp_month = validate_data($_POST["modified_exp_month"],"exp_month");
        if ($exp_month == false)
        {
            $err_msg .= "Expiration month is not in a right format\r\n";
        }
        else
        {
            $sql = "update customers set c_exp_month='".$exp_month."' where customer_id='".$cus_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                $err_msg .= "ERROR: updating expiration month of customer's id ".$cus_id."\r\n";
            }
        }
    }
    if ($_POST["modified_exp_year"] != '')
    {
        /*Need to validate data before making any changes*/
        $exp_year = validate_data($_POST["modified_exp_year"],"exp_year");
        if ($exp_year == false)
        {
            $err_msg .= "Expiration year is not in a right format\r\n";
        }
        else
        {
            $sql = "update customers set c_exp_year='".$exp_year."' where customer_id='".$cus_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                $err_msg .= "ERROR: updating expiration year of customer's id ".$cus_id."\r\n";
            }
        }
    }
    if ($_POST["modified_street_addr_billing"] != '')
    {
        /*Need to validate data before making any changes*/
        $address_billing = validate_data($_POST["modified_street_addr_billing"],"address");
        if ($address_billing == false)
        {
            $err_msg .= "Billing street address is not in a right format\r\n";
        }
        else
        {
            $sql = "update customers set c_street_addr_billing='".$address_billing."' where customer_id='".$cus_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                $err_msg .= "ERROR: updating billing street address of customer's id ".$cus_id."\r\n";
            }
        }
    }
    if ($_POST["modified_city_billing"] != '')
    {
        /*Need to validate data before making any changes*/
        $city_billing = validate_data($_POST["modified_city_billing"],"city");
        if ($city_billing == false)
        {
            $err_msg .= "Billing city is not in a right format\r\n";
        }
        else
        {
            $sql = "update customers set c_city_billing='".$city_billing."' where customer_id='".$cus_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                $err_msg .= "ERROR: updating billing city of customer's id ".$cus_id."\r\n";
            }
        }
    }
    if ($_POST["modified_state_billing"] != '')
    {
        /*Need to validate data before making any changes*/
        $state_billing = validate_data($_POST["modified_state_billing"],"state");
        if ($state_billing == false)
        {
            $err_msg .= "Billing state is not in a right format\r\n";
        }
        else
        {
            $sql = "update customers set c_state_billing='".$state_billing."' where customer_id='".$cus_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                $err_msg .= "ERROR: updating billing state of customer's id ".$cus_id."\r\n";
            }
        }
    }
    if ($_POST["modified_country_billing"] != '')
    {
        /*Need to validate data before making any changes*/
        $country_billing = validate_data($_POST["modified_country_billing"],"country");
        if ($country_billing == false)
        {
            $err_msg .= "Billing country is not in a right format\r\n";
        }
        else
        {
            $sql = "update customers set c_country_billing='".$country_billing."' where customer_id='".$cus_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                $err_msg .= "ERROR: updating billing country of customer's id ".$cus_id."\r\n";
            }
        }
    }
    if ($_POST["modified_phone"] != '')
    {
        /*Need to validate data before making any changes*/
        $phone = validate_data($_POST["modified_phone"],"phone");
        if ($phone == false)
        {
            $err_msg .= "Phone number is not in a right format\r\n";
        }
        else
        {
            $sql = "update customers set c_phone='".$phone."' where customer_id='".$cus_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                $err_msg .= "ERROR: updating phone number of customer's id ".$cus_id."\r\n";
            }
        }
    }
    if ($_POST["modified_password"] != '')
    {
        /*Need to validate data before making any changes*/
        $password = validate_data($_POST["modified_password"],"password");
        if ($password == false)
        {
            $err_msg .= "password is not in a right format\r\n";
        }
        else
        {
            $sql = "update customers set c_password=password('".$password."') where customer_id='".$cus_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                $err_msg .= "ERROR: updating password of customer's id ".$cus_id."\r\n";
            }
        }
    }
    if ($err_msg != "")
    {
        echo '<p style="color: red">'.$err_msg.'</p>';
    }
    else
    {
        echo '<p style="color: blue">Successfully update your profile!</p>';
    }
    disconnectDB($conn);
    display_edit_profile_page();
}

/*Function to validate data*/
function validate_data( $data, $type )
{
    $data = trim($data); //remove whitespaces
    $data = stripslashes($data); //remove all backslashes
    $data = htmlspecialchars($data);
    if ($type == "first_name" || $type == "last_name" || $type == "city" || $type == "state" || $type == "country")
    {
        return filter_var($data,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/\D+/")));
    }
    else if ($type == "address")
    {
        return filter_var($data,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/([0-9])+\s+([A-Za-z])+.*/")));
    }
    else if ($type == "credit_card")
    {
        return filter_var($data,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/[0-9]{16}/")));
    }
    else if ($type == "security_code")
    {
        return filter_var($data,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/[0-9]{3}/")));
    }
    else if ($type == "exp_month")
    {
        return filter_var($data,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/[0-9]{1,2}/")));
    }
    else if ($type == "exp_year")
    {
        return filter_var($data,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/[0-9]{4}/")));
    }
    else if ($type == "phone")
    {
        return filter_var($data,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/[0-9]{10}/")));
    }
    else if ($type == "email")
    {
        return filter_var($data,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/[A-Za-z0-9]+@[A-Za-z0-9]+\.[a-z]{2,3}$/")));
    }
    else if ($type == "username")
    {
        return filter_var($data,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/[a-zA-Z0-9]+/")));
    }
    return $data;
}

?>