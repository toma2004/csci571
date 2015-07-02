<?php
/**
 * Created by PhpStorm.
 * User: NguyenTran
 * Date: 6/30/2015
 * Time: 9:39 PM
 */

/*Establish my session array*/
if(!isset($_SESSION))
{
    session_start();
}


if (isset($_POST["special_sale_display"]))
{
    display_special_sale_main_page();
}
else if (isset($_POST["product_name_clicked"]))
{
    display_detail_product($_POST["product_name_clicked"]);
}
else if (isset($_POST["sign_up_user_name"]))
{
    checkUnique('user_name');
}
else if (isset($_POST["sign_up_email"]))
{
    checkUnique('email');
}
else if (isset($_POST["dob"]))
{
    /*Signal to add new customer to our database*/
    add_new_customer();
}
else
{
    /*If user log in, launch main_webpage_logged_in.html
    * Else, launch original main webpage
    */
    if (!isset($_SESSION['last_activity']) || !isset($_SESSION['username']) || !isset($_SESSION['password']) || !isset($_SESSION['timeout']))
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
            require "main_webpage_logged_in.html";
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

/*Function to display special sale items on main page*/
function display_special_sale_main_page()
{
    $conn = connectDB();

    /*Check to see if there is any product on sale*/
    $sql = "select special_sale_id, product_id from special_sales_and_product";
    $res = mysql_query($sql);
    if (!$res)
    {
        #There is no product on sale
        echo '<p style="color: red"> There is no product on sale at the moment</p>';
    }
    else
    {
        while($row = mysql_fetch_assoc($res))
        {
            /*Getting image and price*/
            $sql = "select product_name,product_price,product_image from products where product_id='".$row["product_id"]."'";
            $res_product = mysql_query($sql);
            if($res_product)
            {
                $row_product = mysql_fetch_assoc($res_product);
                /*Getting special sale percentage discount*/
                $sql = "select percentage_discount from special_sales where special_sale_id='".$row["special_sale_id"]."'";
                $res_special_sale = mysql_query($sql);
                if($res_special_sale)
                {
                    $row_special_sale = mysql_fetch_assoc($res_special_sale);

                    /*start to display it*/
                    echo '<div class="image_block">';
                    echo '<input type="image" src="'.$row_product["product_image"].'" alt="'.$row_product["product_name"].'" style="width: 200px; height: 200px;" name="product_name_clicked" value="'.$row["product_id"].'">';
                    echo '<span class="caption"><span style="color: red">SALE: '.$row_special_sale["percentage_discount"].' %OFF</span><br/>';
                    echo 'Orig. price = '.$row_product["product_price"].'<br/>';
                    $discounted = (1 - ($row_special_sale["percentage_discount"] / 100)) * $row_product["product_price"];
                    echo '<span style="color: red">Discounted price = '.$discounted.'</span></span></div>';
                }
            }
        }
    }
    disconnectDB($conn);
}

/*Function to display a detail of product given product id*/
function display_detail_product( $product_id )
{
    $conn = connectDB();

    /*retrieve product info*/
    $sql = "select product_name, product_description, ingredients, product_image from products where product_id ='".$product_id."'";
    $res = mysql_query($sql);
    if(!$res)
    {
        echo '<p style="color: red"> ERROR: retrieving product information</p>';
    }
    else
    {
        if(! ($row = mysql_fetch_assoc($res)))
        {
            echo '<p style="color: red"> ERROR: There is no product found</p>';
        }
        else
        {
            /*Check category before displaying*/
            $sql = "select category_id from product_and_category where product_id = '".$product_id."'";
            $res_category = mysql_query($sql);
            $counter = 0;
            $category_id_arr = array();
            while ($row_category = mysql_fetch_assoc($res_category))
            {
                $counter += 1;
                array_push($category_id_arr,$row_category["category_id"]);
            }
            if ($counter == 0)
            {
                echo '<p style="color: red"> ERROR: There is no product category found</p>';
            }
            else
            {
                $category_name_str = '';
                $category_name_arr = array();
                foreach ($category_id_arr as $val)
                {
                    $sql = "select category_name from product_categories where category_id = '".$val."'";
                    $res_category_info = mysql_query($sql);
                    if (! ($row_category_info = mysql_fetch_assoc($res_category_info)))
                    {
                        echo '<p style="color: red"> ERROR: retrieving product category information</p>';
                        disconnectDB($conn);
                        return;
                    }
                    else
                    {
                        array_push($category_name_arr,$row_category_info["category_name"]);
                    }
                }
                $category_name_str = implode(",", $category_name_arr);

                if ($category_name_str == '')
                {
                    echo '<p style="color: red"> ERROR: There is no product category associated with this product</p>';
                }
                else
                {
                    ?>

                    <!-- End php and display html -->
                    <!DOCTYPE html>
                    <html>
                    <head lang="en">
                        <meta charset="UTF-8"/>
                        <meta name="author" content="Nguyen Tran"/>

                        <link rel="stylesheet" type="text/css" href="main_css.css"/> <!-- link to external css file
                                <!--<link rel="shortcut icon" type="image/jpg" href="flower_crown1.jpg"></link> -->
                        <title>Product info</title>
                    </head>
                    <body>
                        <form id="display_product_info" action="main_page.php" method="POST">
                    <?php
                    /*start to display product info*/
                    echo '<img src="'.$row["product_image"].'" alt="'.$row["product_name"].'" style="width: 400px; height: 200px;"><br/>';

                    echo 'Product name: ';
                    echo '<span style="position:absolute; left: 13%">'.$row["product_name"].'</span><br/>';

                    echo '<p class="display_product_info">';
                    echo 'Product description: ';
                    echo '<textarea rows="20" cols="30" readonly style="position:relative; left: 5%">'.$row["product_description"].'</textarea>';

                    echo '<span style="position: absolute; left: 40%">Ingredient: </span>';
                    echo '<textarea rows="20" cols="30" readonly style="position: absolute; left: 50%">'.$row["ingredients"].'</textarea></p><br/>';

                    echo '<p class="display_product_info">';
                    echo 'Belong to category: ';
                    echo '<textarea rows="20" cols="30" readonly style="position:relative; left: 5.5%">'.$category_name_str.'</textarea></p><br/>';

                    /*See if this product has any special sale. If yes, display that info too*/
                    $sql = "select special_sale_id from special_sales_and_product where product_id = '".$product_id."'";
                    $res_special_sale = mysql_query($sql);
                    if ($res_special_sale)
                    {
                        if ($row_special_sale = mysql_fetch_assoc($res_special_sale))
                        {
                            $sql = "select start_date, end_date, percentage_discount from special_sales where special_sale_id = '".$row_special_sale["special_sale_id"]."'";
                            $res_special_sale_info = mysql_query($sql);
                            if ($row_special_sale_info = mysql_fetch_assoc($res_special_sale_info))
                            {
                                echo 'This product '.$row["product_name"]. ' has special sale event discount at <span style="color: red">'.$row_special_sale_info["percentage_discount"].'% from '.$row_special_sale_info["start_date"].' to '.$row_special_sale_info["end_date"].'</span><br/>';
                            }
                        }
                    }
                    echo 'ACT FAST and ORDER YOURS TODAY WHILE SUPPLIES LAST!<br/><br/>';

                    echo '<button type="submit">Home</button>';
                    echo '<button type="button" style="position:relative; left:15px;">Add to Cart</button>';
                    ?>
                            </form>
                    </body>
                    </html>

                    <?php
                }
            }
        }
    }
    disconnectDB($conn);
}


/*Function return true if a given argument is unique in the database. Else return false*/
function checkUnique( $val )
{
    $conn = connectDB();
    $isUnique = 'true';
    if ($val == "user_name")
    {
        $sql = "select * from customers where c_username='".$_POST["sign_up_user_name"]."' ";
        $res = mysql_query($sql);
        if ($res)
        {
            if ($row = mysql_fetch_assoc($res))
            {

                $isUnique =  'false';
            }
        }
    }
    else if ($val == "email")
    {
        $sql = "select * from customers where c_email='".$_POST["sign_up_email"]."' ";
        $res = mysql_query($sql);
        if ($res)
        {
            if ($row = mysql_fetch_assoc($res))
            {
                $isUnique = 'false';
            }
        }
    }
    echo $isUnique;
    disconnectDB($conn);
}

/*Function to add new customer to our database*/
function add_new_customer()
{
    $conn = connectDB();
    if (isset($_POST["first_name"]) && isset($_POST["last_name"]) && isset($_POST["addr_shipping"]) && isset($_POST["city_shipping"]) && isset($_POST["state_shipping"]) && isset($_POST["country_shipping"]) && isset($_POST["dob"]) && isset($_POST["mycreditcard"]) && isset($_POST["mysecuritycode"]) && isset($_POST["myexpiredate_month"]) && isset($_POST["myexpiredate_year"]) && isset($_POST["addr_billing"]) && isset($_POST["city_billing"]) && isset($_POST["state_billing"]) && isset($_POST["country_billing"]) && isset($_POST["phone"]) && isset($_POST["email_addr"]) && isset($_POST["usr"]) && isset($_POST["pass"]))
    {
        $fname = validate_data($_POST["first_name"],"first_name");
        $lname = validate_data($_POST["last_name"],"last_name");
        $addr_shipping = validate_data($_POST["addr_shipping"],"address");
        $city_shipping = validate_data($_POST["city_shipping"],"city");
        $state_shipping = validate_data($_POST["state_shipping"],"state");
        $country_shipping = validate_data($_POST["country_shipping"],"country");
        $dob = validate_data($_POST["dob"],"dob");
        $credit_card = validate_data($_POST["mycreditcard"],"credit_card");
        $security_code = validate_data($_POST["mysecuritycode"],"security_code");
        $exp_month = validate_data($_POST["myexpiredate_month"],"exp_month");
        $exp_year = validate_data($_POST["myexpiredate_year"],"exp_year");
        $addr_billing = validate_data($_POST["addr_billing"],"address");
        $city_billing = validate_data($_POST["city_billing"],"city");
        $state_billing = validate_data($_POST["state_billing"],"state");
        $country_billing = validate_data($_POST["country_billing"],"country");
        $phone = validate_data($_POST["phone"],"phone");
        $email = validate_data($_POST["email_addr"],"email");
        $username = validate_data($_POST["usr"],"username");
        $password = validate_data($_POST["pass"],"password");

        /*validate date*/
        $dob_arr = explode('-',$dob);

        if ($fname == false || $lname == false || $addr_shipping == false || $city_shipping == false || $state_shipping == false || $country_shipping == false || $dob == false || $credit_card == false || $security_code == false || $exp_month == false || $exp_year == false || $addr_billing == false || $city_billing == false || $state_billing == false || $country_billing == false || $phone == false || $email == false || $username == false || $password == false)
        {
            #error in one of the entries
            require "pre_sign_up_page.html";
            echo "ERROR: one of the entries is not valid";
            require "post_sign_up_page.html";
        }
        else if (!checkdate($dob_arr[1],$dob_arr[2],$dob_arr[0]))
        {
            #error in one of the entries
            require "pre_sign_up_page.html";
            echo "ERROR: Date of Birth is not in correct format";
            require "post_sign_up_page.html";
        }
        else
        {
            $sql = "insert into customers (c_first_name, c_last_name, c_street_addr_shipping, c_city_shipping, c_state_shipping, c_country_shipping, c_dob, c_credit_card, c_security_code, c_exp_month, c_exp_year, c_street_addr_billing, c_city_billing, c_state_billing, c_country_billing, c_phone, c_email, c_username, c_password) values ('".$fname."','".$lname."','".$addr_shipping."','".$city_shipping."','".$state_shipping."','".$country_shipping."','".$dob."','".$credit_card."','".$security_code."','".$exp_month."','".$exp_year."','".$addr_billing."','".$city_billing."','".$state_billing."','".$country_billing."','".$phone."','".$email."','".$username."',password('".$password."'))";
            $res = mysql_query($sql);
            if (!$res)
            {
                require "pre_sign_up_page.html";
                echo "ERROR: Inserting new customer";
                require "post_sign_up_page.html";
            }
            else
            {
                #Go to log in page
                require "pre_log_in_page.html";
                echo "You have successfully signed up. Do you want to log in now?";
                require "post_log_in_page.html";
            }
        }
    }
    else
    {
        require "pre_sign_up_page.html";
        echo "ERROR: one of the entries is empty or not valid";
        require "post_sign_up_page.html";
    }
    disconnectDB($conn);
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