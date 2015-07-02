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
    /*They are logged in. Display info for them to modify*/
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
                    echo '<input type="text" id="modified_first_name" name="modified_first_name" maxlength="30" pattern="\D+" style="position:absolute; left: 30%"/><br/><br/>';

                    echo 'Your last name: ';
                    echo '<span style="position:absolute; left: 15%">'.$row["c_last_name"].'</span>';
                    echo '<input type="text" id="modified_last_name" name="modified_last_name" maxlength="30" pattern="\D+" style="position:absolute; left: 30%"/><br/><br/>';

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

                    echo '<a href="main_webpage_logged_in.html"><button type="button">Home</button></a>';
                    echo '<button type="submit" id="submit_edit_profile_form" style="position: relative; left: 10px">Submit</button>';
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