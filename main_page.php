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

/*Display special sale on main page*/
if (isset($_POST["special_sale_display"]))
{
    display_special_sale_main_page();
}
/*Display product category after user clicks on one of the category*/
else if (isset($_POST["display_category_id"]))
{
    display_product_category();
}
/*Display drop down list for product category based on our database. Dynamic HTML*/
else if (isset($_POST["list_category_display"]))
{
    display_list_product_category();
}
/*Display detail of a product when user clicks on a product image*/
else if (isset($_POST["product_name_clicked"]))
{
    display_detail_product($_POST["product_name_clicked"]);
}
/*Check if user name and email are unique in our database*/
else if (isset($_POST["sign_up_user_name"]))
{
    checkUnique('user_name');
}
else if (isset($_POST["sign_up_email"]))
{
    checkUnique('email');
}
/*Signal that user is creating new profile*/
else if (isset($_POST["dob"]))
{
    /*Signal to add new customer to our database*/
    add_new_customer();
}
/*Check if user added a product to a cart*/
else if (isset($_POST["add_to_cart"]))
{
    add_to_cart( $_POST["hidden_add_to_cart_pid"] );
    /*After adding product to cart. Re-direct customer to main homepage*/
    direct_homepage();
}
/*Function to display shopping cart upon customer's request to view/edit their cart*/
else if (isset($_POST["display_shopping_cart"]))
{
    display_shopping_cart();
}
/*Function to change quantity of a product in customer's shopping cart*/
else if (isset($_POST["change_shopping_cart_product_id"]) && isset($_POST["quantity"]))
{
    change_quantity_of_product_shopping_cart();
}
/*Function to remove an item from shopping cart*/
else if (isset($_POST["remove_item_product_id"]))
{
    remove_item_from_shopping_cart();
}
/*Function to remove the entire shopping cart*/
else if (isset($_POST["remove_entire_cart"]))
{
    remove_entire_shopping_cart();
}
/*Function to reply to customer's request to checkout*/
else if (isset($_POST["request_for_checkout"]))
{
    display_checkout_summary();
}
/*If customer want to edit profile from checkout page, link his/her to edit_profile.php*/
else if (isset($_POST["edit_profile_from_checkout"]))
{
    require "edit_profile.php";
}
/*If customer wants to log in to check out their item. Link to customer_login.php*/
else if (isset($_POST["customer_log_in_to_checkout"]))
{
    require "customer_login.php";
}
/*Customer has placed order. Save order info to database and clear shopping cart*/
else if (isset($_POST["place_order"]))
{
    place_order();
}
/*function to display past order to customer*/
else if (isset($_POST["request_for_past_order"]))
{
    display_past_order();
}
/*Function to display detail of a past order*/
else if (isset($_POST["request_past_order_detail"]))
{
    display_past_order_detail($_POST["request_past_order_detail"]);
}
else
{
    /*If user log in, launch main_webpage_logged_in.html
    * Else, launch original main webpage
    */
    direct_homepage();

}

/*Direct to home page*/
function direct_homepage()
{
    if (!isset($_SESSION['last_activity']) || !isset($_SESSION['username']) || !isset($_SESSION['password']) || !isset($_SESSION['timeout']))
    {
        if (isset($_POST["add_to_cart"]))
        {
            require "pre_main_webpage.html";
            echo '<p style="color: blue">Successfully added product(s) to your cart</p>';
            require "post_main_webpage.html";
        }
        else
        {
            require "main_webpage.html";
        }
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
            if (isset($_POST["add_to_cart"]))
            {
                require "pre_main_webpage_logged_in.html";
                echo '<p style="color: blue">Successfully added product(s) to your cart</p>';
                require "post_main_webpage_logged_in.html";
            }
            else
            {
                require "main_webpage_logged_in.html";
            }
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
                $stillOnSale = false;
                $row_product = mysql_fetch_assoc($res_product);
                /*Getting special sale percentage discount*/
                $sql = "select * from special_sales where special_sale_id='".$row["special_sale_id"]."'";
                $res_special_sale = mysql_query($sql);
                if($res_special_sale)
                {
                    $row_special_sale = mysql_fetch_assoc($res_special_sale);

                    if (check_my_date($row_special_sale["start_date"], "after") && check_my_date($row_special_sale["end_date"], "before"))
                    {
                        /*start to display it*/
                        echo '<div class="image_block">';
                        echo '<input type="image" src="'.$row_product["product_image"].'" alt="'.$row_product["product_name"].'" style="width: 200px; height: 200px;" name="product_name_clicked" value="'.$row["product_id"].'">';
                        echo '<span class="caption"><span style="color: red">SALE: '.$row_special_sale["percentage_discount"].' %OFF</span><br/>';
                        echo 'Orig. price = '.$row_product["product_price"].'<br/>';
                        $discounted = (1 - ($row_special_sale["percentage_discount"] / 100)) * $row_product["product_price"];
                        $discounted = number_format($discounted, 2, '.', ',');
                        echo '<span style="color: red">Discounted price = '.$discounted.'</span></span></div>';
                    }
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
                            <input type="hidden" name="hidden_add_to_cart_pid" value="<?php echo $product_id; ?>"/>
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
                                if (check_my_date($row_special_sale_info["start_date"], "after") && check_my_date($row_special_sale_info["end_date"], "before"))
                                {
                                    echo 'This product '.$row["product_name"]. ' has special sale event discount at <span style="color: red">'.$row_special_sale_info["percentage_discount"].'% from '.$row_special_sale_info["start_date"].' to '.$row_special_sale_info["end_date"].'</span><br/>';
                                }
                            }
                        }
                    }
                    echo 'ACT FAST and ORDER YOURS TODAY WHILE SUPPLIES LAST!<br/><br/>';

                    echo '<button type="submit">Home</button>';
                    echo '<button type="submit" name="add_to_cart" value="add_to_cart" style="position:relative; left:15px;">Add to Cart</button>';
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

/*Function to display a product category*/
function display_product_category()
{
    $conn = connectDB();
    $category_id = $_POST["display_category_id"];

    $sql = "select product_id from product_and_category where category_id='".$category_id."'";
    $res = mysql_query($sql);
    if (!$res)
    {
        echo '<p style="color: red">ERROR: retrieving products for given product category</p>';
    }
    else
    {
        $counter = 0;
        while ($row = mysql_fetch_assoc($res))
        {
            $counter += 1;
            #For each product_id, do a search
            $sql = "select * from products where product_id='".$row["product_id"]."'";
            $res_product_id = mysql_query($sql);

            if ($res_product_id)
            {
                if ($row_product = mysql_fetch_assoc($res_product_id))
                {
                    $hasSpecial_sale = false;
                    echo '<div class="image_block">';
                    echo '<input type="image" src="'.$row_product["product_image"].'" alt="'.$row_product["product_name"].'" style="width: 200px; height: 200px;" name="product_name_clicked" value="'.$row["product_id"].'">';
                    /*Check to see if this product is on sale*/
                    $sql = "select special_sale_id from special_sales_and_product where product_id='".$row["product_id"]."'";
                    $res_special_sale_id = mysql_query($sql);
                    if ($res_special_sale_id)
                    {
                        if ($row_special_sale_id = mysql_fetch_assoc($res_special_sale_id))
                        {
                            #Now get info about this special sale id
                            $sql = "select * from special_sales where special_sale_id='".$row_special_sale_id["special_sale_id"]."'";
                            $res_special_sale = mysql_query($sql);
                            if ($res_special_sale)
                            {
                                if ($row_special_sale = mysql_fetch_assoc($res_special_sale))
                                {
                                    if (check_my_date($row_special_sale["start_date"], "after") && check_my_date($row_special_sale["end_date"], "before"))
                                    {
                                        $hasSpecial_sale = true;
                                        echo '<span class="caption"><span style="color: red">SALE: '.$row_special_sale["percentage_discount"].' %OFF</span><br/>';
                                    }
                                 }
                            }
                        }
                    }
                    if ($hasSpecial_sale)
                    {
                        echo 'Orig. price = '.$row_product["product_price"].'<br/>';
                        $discounted = (1 - ($row_special_sale["percentage_discount"] / 100)) * $row_product["product_price"];
                        $discounted = number_format($discounted, 2, '.', ',');
                        echo '<span style="color: red">Discounted price = '.$discounted.'</span></span></div>';
                    }
                    else
                    {
                        echo 'Orig. price = '.$row_product["product_price"].'</div>';
                    }
                }
            }
        }
    }

    echo '<br/><br/>';
    echo '<button type="button" onclick=div_transform("form1");>Home</button>';
    disconnectDB($conn);
}

/*Function to display drop down list for product category based on our database*/
function display_list_product_category()
{
    $conn = connectDB();
    $sql = "select category_id, category_name from product_categories";
    $res = mysql_query($sql);
    if($res)
    {
        while ($row = mysql_fetch_assoc($res))
        {
            echo '<li onclick=display_category("'.$row["category_id"].'");><span class="replace_a">'.$row["category_name"].'</span></li>';
        }
    }
    disconnectDB($conn);
}

/*Function to check if a given date is before/after the current date*/
function check_my_date( $date_str, $intr)
{
    date_default_timezone_set('America/Los_Angeles');
    $cur_time = getdate();
    $date_check = explode('-',$date_str);
    #year - $date_check[0]
    #month - $date_check[1]
    #day - $date_check[2]
    if (count($date_check) != 3)
    {
        #Date is not in correct format
        return false;
    }
    else
    {
        foreach ($date_check as $val)
        {
            #Convert to int
            $val = intval($val);
        }
    }
    $y = $cur_time["year"] - $date_check[0];
    $m = $cur_time["mon"] - $date_check[1];
    $d = $cur_time["mday"] - $date_check[2];

    if ($intr == "after") /*Check to see if this date str is after today date*/
    {
        if($y < 0)
        {
            return false;
        }
        else if ($y == 0)
        {
            if ($m < 0)
            {
                return false;
            }
            else if ($m == 0)
            {
                if ($d < 0)
                {
                    return false;
                }
            }
        }
    }
    else if ($intr == "before") /*Check to see if this date str is before today date*/
    {
        if($y > 0)
        {
            return false;
        }
        else if ($y == 0)
        {
            if ($m > 0)
            {
                return false;
            }
            else if ($m == 0)
            {
                if ($d > 0)
                {
                    return false;
                }
            }
        }
    }
    return true;
}

/*Function to add new product to cart using SESSION array*/
function add_to_cart( $product_id )
{
    /*Check if there is a session array*/
    if (isset($_SESSION))
    {
        /*Check if a shopping cart already exists. If not create one*/
        if (isset($_SESSION["shopping_cart"]))
        {
            $index = -1;
            #There is a shopping card.
            #Now check if this product already exists in the cart
            foreach ($_SESSION["shopping_cart"] as $i => $cart_items)
            {
                if ($cart_items["pid"] == $product_id)
                {
                    $index = $i;
                    break;
                }
            }
            /*If the product does not exist, add this new product to our shopping cart array*/
            if ($index == -1)
            {
                array_push($_SESSION["shopping_cart"], array("qty" => "1", "pid" => $product_id));
            }
            else
            {
                /*Product already exists. Update the quantity*/
                $_SESSION["shopping_cart"][$index]["qty"] += 1;
            }
        }
        else
        {
            #This is the first time user add a product to a cart. Create shopping cart
            $_SESSION["shopping_cart"][] = array("qty" => "1", "pid" => $product_id);
        }
    }
    else
    {
        #error session array is not set
    }
}

/*Function to display shopping cart info*/
function display_shopping_cart()
{
    $conn = connectDB();
    $px_from_top = 100;
    /*Check if customer has ever added any product to the shopping cart*/
    if (!isset($_SESSION["shopping_cart"]))
    {
        echo '<p style="color: red; font-weight: bold;">Your shopping cart is empty</p>';
    }
    else
    {
        $counter = 0;
        echo '<span style="position: absolute; left: 550px;">Price</span>';
        echo '<span style="position: absolute; left: 680px;">Quantity</span><br/>';
        foreach ($_SESSION["shopping_cart"] as $cart_items)
        {
            /*Getting product info before display*/
            $sql = "select product_name, product_price, product_image from products where product_id='".$cart_items["pid"]."'";
            $res = mysql_query($sql);
            if ($res)
            {
                if ($row = mysql_fetch_assoc($res))
                {
                    $discounted = $row["product_price"];

                    /*Check if this product is on sale. If yes, display the sale price*/
                    $sql = "select special_sale_id from special_sales_and_product where product_id='".$cart_items["pid"]."'";
                    $res_special_sale_id = mysql_query($sql);
                    if ($res_special_sale_id)
                    {
                        /*If it has a special sale, check date to make sure sale is still valid*/
                        if ($row_special_sale_id = mysql_fetch_assoc($res_special_sale_id))
                        {
                            $sql = "select * from special_sales where special_sale_id='".$row_special_sale_id["special_sale_id"]."'";
                            $res_special_sale = mysql_query($sql);
                            if ($res_special_sale)
                            {
                                if ($row_special_sale = mysql_fetch_assoc($res_special_sale))
                                {
                                    if (check_my_date($row_special_sale["start_date"], "after") && check_my_date($row_special_sale["end_date"], "before"))
                                    {
                                        $discounted = (1 - ($row_special_sale["percentage_discount"] / 100)) * $row["product_price"];
                                        $discounted = number_format($discounted, 2, '.', ',');
                                    }
                                }
                            }
                        }
                    }
                }
            }
            /*start to display each product and its quantity in shopping card*/
            if ($counter == 0)
            {
                echo '<div class="shopping_cart_item">';
            }
            else
            {
                $px_from_top += 120;
                echo '<div class="shopping_cart_item" style="position: absolute; top: '.$px_from_top.'px;">';
            }
            $counter += 1;

            echo '<div class="other_components">';
            echo '<img src="'.$row["product_image"].'" height="100px" width="100px"></div>';

            echo '<div class="pname">';
            echo '<span>'.$row["product_name"].'</span></div>';

            echo '<div class="other_components">';
            echo '<span style="position: absolute; left: 550px; color: red;">'.$discounted.'</span>';
            echo '<select style="position: absolute; left: 690px;" onchange=change_quality("'.$cart_items["pid"].'",this.selectedIndex);>';
            /*Set default selected based on product quantity in the cart*/
            echo '<option value="1">1</option>';
            if ($cart_items["qty"] == 2)
            {
                echo '<option value="2" selected="selected">2</option>';
            }
            else
            {
                echo '<option value="2">2</option>';
            }
            if ($cart_items["qty"] == 3)
            {
                echo '<option value="3" selected="selected">3</option>';
            }
            else
            {
                echo '<option value="3">3</option>';
            }
            if ($cart_items["qty"] == 4)
            {
                echo '<option value="4" selected="selected">4</option>';
            }
            else
            {
                echo '<option value="4">4</option>';
            }
            if ($cart_items["qty"] == 5)
            {
                echo '<option value="5" selected="selected">5</option></select></div>';
            }
            else
            {
                echo '<option value="5">5</option></select></div>';
            }
            echo '<button type="button" class="delete_id" onclick=delete_product_cart("'.$cart_items["pid"].'");>delete</button></div>';
        }

        if ($counter == 0)
        {
            echo '<p style="color: red; font-weight: bold;">Your shopping cart is empty</p>';
        }
        else
        {
            $px_from_top += 120;
            echo '<button type="button" style="position: absolute; top: '.$px_from_top.'px; left: 0;" onclick=delete_entire_cart();>Remove cart</button>';
        }
    }
    disconnectDB($conn);
}

/*Function to update product quantity in customer's shopping cart*/
function change_quantity_of_product_shopping_cart()
{
    /*validate these data which have to be an INT*/
    $product_id = filter_input(INPUT_POST,"change_shopping_cart_product_id",FILTER_VALIDATE_INT);
    $quantity_changed = filter_input(INPUT_POST,"quantity",FILTER_VALIDATE_INT);
    if ($product_id == NULL || $product_id == false || $quantity_changed == NULL || $quantity_changed == false)
    {
        echo 'fail';
    }
    else
    {
        if (isset($_SESSION["shopping_cart"]))
        {
            $index = index_productID_shopping_cart ($product_id);
            if ($index == -1)
            {
                /*Strange error where we could not find the product in our shopping cart*/
                echo 'fail';
            }
            else
            {
                $_SESSION["shopping_cart"][$index]["qty"] = $quantity_changed;
            }
        }
        else
        {
            echo 'fail';
        }
    }
}

/*Function to remove an item from shopping cart*/
function remove_item_from_shopping_cart()
{
    $product_id = filter_input(INPUT_POST,"remove_item_product_id",FILTER_VALIDATE_INT);
    if ($product_id == NULL || $product_id == false)
    {
        echo 'fail';
    }
    else
    {
        if (isset($_SESSION["shopping_cart"]))
        {
            $index = index_productID_shopping_cart ($product_id);
            if ($index == -1)
            {
                /*Strange error where we could not find the product in our shopping cart*/
                echo 'fail';
            }
            else
            {
                /*remove this product id from our shopping cart*/
                unset($_SESSION["shopping_cart"][$index]);
            }
        }
        else
        {
            echo 'fail';
        }
    }
}

/*Function to return an index of array that contain the product id we are looking for in a shopping cart*/
function index_productID_shopping_cart ( $product_id )
{
    if (isset($_SESSION["shopping_cart"]))
    {
        $index = -1;
        foreach ($_SESSION["shopping_cart"] as $i => $cart_items)
        {
            if ($cart_items["pid"] == $product_id)
            {
                $index = $i;
                break;
            }
        }
        return $index;
    }
    else
    {
        return -1;
    }
}

/*Function to remove entire shopping cart*/
function remove_entire_shopping_cart()
{
    if (isset($_SESSION["shopping_cart"]))
    {
        foreach ($_SESSION["shopping_cart"] as $i => $cart_items)
        {
            unset ($cart_items["pid"]);
            unset ($cart_items["qty"]);
            unset ($_SESSION["shopping_cart"][$i]);
        }
        echo 'success';
    }
    else
    {
        echo 'fail';
    }
}

/*Function to display checkout summary if customer has logged in*/
function display_checkout_summary()
{
    /*Check if customer logged in*/
    if (isset($_SESSION["username"]) && isset($_SESSION["password"]))
    {
        /*Check if there is something in shopping cart for checkout*/
        if (isset($_SESSION["shopping_cart"]) && count($_SESSION["shopping_cart"]) > 0)
        {
            $conn = connectDB();
            /*Getting customer info*/
            $sql = "select * from customers where customer_id='".$_SESSION["cus_id"]."'";
            $res_cus_id = mysql_query($sql);
            if ($res_cus_id)
            {
                if ($row_cus_id = mysql_fetch_assoc($res_cus_id))
                {
                    echo '<div id="customer_shopping_cart_info">';
                    echo '<div id="customer_info">';
                    echo '<div id="shipping_addr">';
                    echo '<span>Shipping address</span><br/>';
                    echo '<span>'.$row_cus_id["c_first_name"].' '.$row_cus_id["c_last_name"].'</span><br/>';
                    echo '<span>'.$row_cus_id["c_street_addr_shipping"].'</span><br/>';
                    echo '<span>'.$row_cus_id["c_city_shipping"].', '.$row_cus_id["c_state_shipping"].'</span><br/>';
                    echo '<span>'.$row_cus_id["c_country_shipping"].'</span><br/>';
                    echo '<span>Phone: '.$row_cus_id["c_phone"].'</span></div>';

                    echo '<div id="billing_addr">';
                    echo '<span>Billing address</span><br/>';
                    echo '<span>'.$row_cus_id["c_first_name"].' '.$row_cus_id["c_last_name"].'</span><br/>';
                    echo '<span>'.$row_cus_id["c_street_addr_billing"].'</span><br/>';
                    echo '<span>'.$row_cus_id["c_city_billing"].', '.$row_cus_id["c_state_billing"].'</span><br/>';
                    echo '<span>'.$row_cus_id["c_country_billing"].'</span></div>';

                    echo '<div id="payment_method">';
                    echo '<span>Credit card ending in '.substr($row_cus_id["c_credit_card"],-4).'</span><br/>';
                    echo '<button type="submit" id="edit_info" name="edit_profile_from_checkout" value="edit_profile_from_checkout">Edit info</button><br/></div></div>'; //End div customer_info

                    #Start of div=shopping_cart_info
                    /*Need to calculate total height for this div*/
                    $elements = count($_SESSION["shopping_cart"]);
                    $initial_height = 10 + ($elements * 120) + 40; /*First product will be 10px from top. Each of the following (including space for the "edit cart" button) will be += 120px from top. We will also add extra 40px at the end to create margin*/
                    echo '<div id="shopping_cart_info" style="height: '.$initial_height.'px;">';
                    echo '<span style="position: absolute; left: 70%; top: 1.5%;">Price</span>';
                    echo '<span style="position: absolute; left: 77%; top: 1.5%;">Quantity</span><br/>';
                    /*display each item in shopping cart*/
                    $counter = 0;
                    $myheight = 0;
                    $total_amount = 0;
                    foreach ($_SESSION["shopping_cart"] as $i => $cart_items)
                    {
                        $isSpecialSale = false;
                        /*Getting product info before display*/
                        $sql = "select product_name, product_price, product_image from products where product_id='".$cart_items["pid"]."'";
                        $res = mysql_query($sql);
                        if ($res)
                        {
                            if ($row = mysql_fetch_assoc($res))
                            {
                                $discounted = $row["product_price"];

                                /*Check if this product is on sale. If yes, display the sale price*/
                                $sql = "select special_sale_id from special_sales_and_product where product_id='".$cart_items["pid"]."'";
                                $res_special_sale_id = mysql_query($sql);
                                if ($res_special_sale_id)
                                {
                                    /*If it has a special sale, check date to make sure sale is still valid*/
                                    if ($row_special_sale_id = mysql_fetch_assoc($res_special_sale_id))
                                    {
                                        $sql = "select * from special_sales where special_sale_id='".$row_special_sale_id["special_sale_id"]."'";
                                        $res_special_sale = mysql_query($sql);
                                        if ($res_special_sale)
                                        {
                                            if ($row_special_sale = mysql_fetch_assoc($res_special_sale))
                                            {
                                                if (check_my_date($row_special_sale["start_date"], "after") && check_my_date($row_special_sale["end_date"], "before"))
                                                {
                                                    $isSpecialSale = true;
                                                    $discounted = (1 - ($row_special_sale["percentage_discount"] / 100)) * $row["product_price"];
                                                    $discounted = number_format($discounted, 2, '.', ',');
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //update total amount
                        $total_amount += ($discounted * $cart_items["qty"]);
                        if ($counter == 0)
                        {
                            $myheight = 10;
                            echo '<div class="shopping_cart_item" style="position: relative; top: 10px; left: 25px;">';
                            $counter += 1;
                        }
                        else
                        {
                            $myheight += 120;
                            echo '<div class="shopping_cart_item" style="position: relative; top: '.$myheight.'px; left: 25px;">';
                        }
                        echo '<div class="img_product_cart">';
                        echo '<img src="'.$row["product_image"].'" height="100px" width="100px"></div>';
                        echo '<div class="pname_cart">';
                        echo '<span>'.$row["product_name"].'</span></div>';
                        echo '<div class="price_quantity_cart">';
                        echo '<span style="color: red">'.$discounted.'</span>';
                        echo '<span style="position: absolute; left: 100%;">'.$cart_items["qty"].'</span></div></div>'; //End div=shopping_cart_item

                        /*Store new variables to associative array for later "place order" and save to database*/
                        $_SESSION["shopping_cart"][$i]["p_price"] = $discounted;
                        if ($isSpecialSale)
                        {
                            $_SESSION["shopping_cart"][$i]["special_sale_id"] = $row_special_sale_id["special_sale_id"];
                        }
                        else
                        {
                            $_SESSION["shopping_cart"][$i]["special_sale_id"] = 0;
                        }

                    }
                    $myheight += 120;
                    echo '<button type="button" onclick="request_shopping_cart_info()" id="edit_cart_checkout" style="top: '.$myheight.'px;">Edit cart</button></div></div>'; //End div=shooping_cart_info and div=customer_shopping_cart_info

                    //Start div=order_summary
                    echo '<div id="order_summary">';
                    echo '<button type="submit" id="place_order_checkout" name="place_order" value="place_order">Place your order</button><br/>';
                    echo '<span style="font-weight: bold; color: orange; position: relative; left: 5%; top: 5%">Order summary</span><br/>';
                    echo '<span style="position: relative; left: 5%; top: 5%">Item ('.count($_SESSION["shopping_cart"]).'):</span>';
                    echo '<span class="indent_left">$'.$total_amount.'</span><br/>';
                    echo '<span style="position: relative; left: 5%; top: 5%">Shipping & handling:</span>';
                    echo '<span class="indent_left">$5.99</span><br/>';
                    echo '<span style="position: relative; left: 5%; top: 5%">Total before tax:</span>';
                    $total_and_shipping = $total_amount + 5.99;
                    echo '<span class="indent_left">$'.$total_and_shipping.'</span><br/>';
                    echo '<span style="position: relative; left: 5%; top: 5%">Estimated tax to be collected:</span>';
                    echo '<span class="indent_left">$0.00</span><br/>';
                    echo '<span style="font-weight: bold; color: red; position: relative; left: 5%; top: 5%">Order total:</span>';
                    echo '<span class="indent_left" style="font-weight: bold; color: red">$'.$total_and_shipping.'</span></div>'; //End div=order_summary

                    /*Add new SESSION for place order and save database*/
                    $_SESSION["order_total_amount"] = $total_amount;
                    $_SESSION["order_total_tax"] = 0;
                    $_SESSION["order_total_shipping"] = 5.99;
                }
            }
            disconnectDB($conn);
        }
        else
        {
            echo '<p style="color: red">Your shopping cart is empty. Please add a product to your cart before check out</p>';
        }
    }
    else
    {
        /*Display log in prompt for customer to log in*/
        echo '<div  class="outer_box_login">';
        echo '<div class="login_form1">';
        echo '<p id="error_message_log_in_page" style="color: red">Please log in before proceeding to check out</p>';
        echo '<label for="usrname">User Name</label><span style="color: red">*</span>';
        echo '<input type="text" id="usrname" name="user_name" maxlength="30" pattern="[a-zA-z0-9]+" required/><br/>';
        echo '<label for="pwd">Password</label><span style="color: red">*</span>';
        echo '<input type="password" id="pwd" name="pass_word" maxlength="30" required style="position:relative; left:11px;"/><br/><br/>';
        echo '<a href="main_webpage.html"><button type="button">Home</button></a>';
        echo '<button type="submit" onclick="validate_log_in_page()" name="customer_log_in_to_checkout" value="customer_log_in_to_checkout" style="position: relative; left: 10px">Submit</button></div></div>'; //End div=outer_box_login and div=login_form1
    }
}

/*Function to save order info and clear shopping cart when customer has placed an order*/
function place_order()
{
    if (isset($_SESSION["order_total_amount"]) && isset($_SESSION["order_total_tax"]) && isset($_SESSION["order_total_shipping"]))
    {
        $conn = connectDB();
        date_default_timezone_set('America/Los_Angeles');
        $cur_time = getdate();
        $mydate = implode('-', array($cur_time["year"], $cur_time["mon"], $cur_time["mday"]));
        $sql = "insert into orders (order_date,order_total_amount,order_total_tax,order_shipping_cost,customer_id) values ('".$mydate."','".$_SESSION["order_total_amount"]."','".$_SESSION["order_total_tax"]."','".$_SESSION["order_total_shipping"]."','".$_SESSION["cus_id"]."')";
        $res = mysql_query($sql);
        if ($res)
        {
            //Need to retrieve order id
            $sql = "select order_id from orders order by order_id DESC limit 1";
            $res_order_id = mysql_query($sql);
            if ($res_order_id)
            {
                if ($row_order_id = mysql_fetch_assoc($res_order_id))
                {
                    /*Now insert each ordered item into order_items table in database*/
                    $isOk = false;
                    foreach ($_SESSION["shopping_cart"] as $cart_items)
                    {
                        $sql = "insert into order_items values ('".$row_order_id["order_id"]."','".$cart_items["pid"]."','".$cart_items["qty"]."','".$cart_items["p_price"]."','".$cart_items["special_sale_id"]."')";
                        $res_order_item = mysql_query($sql);
                        if ($res_order_item)
                        {
                            $isOk = true;
                        }
                    }
                    /*Only clear shopping cart when we have added all info to database properly*/
                    if ($isOk)
                    {
                        foreach ($_SESSION["shopping_cart"] as $i => $cart_items)
                        {
                            unset ($cart_items["pid"]);
                            unset ($cart_items["qty"]);
                            unset ($cart_items["p_price"]);
                            unset ($cart_items["special_sale_id"]);
                            unset ($_SESSION["shopping_cart"][$i]);
                        }
                        require "pre_main_webpage_logged_in.html";
                        echo '<p style="color: green; font-weight: bold; font-size: 200%;">Your order has been placed successfully. Thank you very much!</p>';
                        require "post_main_webpage_logged_in.html";
                    }
                }
            }
        }
        else
        {
            require "pre_main_webpage_logged_in.html";
            echo '<p style="color: red">ERROR: cannot place your order</p>';
            require "post_main_webpage_logged_in.html";
        }
        disconnectDB($conn);
    }
    else
    {
        require "pre_main_webpage_logged_in.html";
        echo '<p style="color: red">ERROR: cannot place your order</p>';
        require "post_main_webpage_logged_in.html";
    }
}

/*Function to display past order*/
function display_past_order()
{
    $conn = connectDB();

    $sql = "select * from orders where customer_id='".$_SESSION["cus_id"]."'";;
    $res = mysql_query($sql);
    $counter = 0;
    while ($row = mysql_fetch_assoc($res))
    {
        $counter += 1;
        echo 'Order date: <span style="color: #6666ff">'.$row["order_date"].'</span>, order total amount: <span style="color: #6666ff">$'.($row["order_total_amount"] + $row["order_shipping_cost"] + $row["order_total_tax"]).'</span>,...';
        echo '<button type="button" class="more_info_past_order" onclick=request_detail_past_order("'.$row["order_id"].'");>(more)</button><br/><br/>';
    }
    if ($counter == 0)
    {
        echo '<p style="color: red">You have not made any order before.</p>';
    }

    disconnectDB($conn);
}

/*Function to display detail of a past order*/
function display_past_order_detail( $order_id )
{
    $conn = connectDB();

    /*Getting order info from order id*/
    $sql = "select * from orders where order_id='".$order_id."'";
    $res_order_id = mysql_query($sql);
    if ($res_order_id)
    {
        if ($row_order_id = mysql_fetch_assoc($res_order_id))
        {
           /*Getting customer's info to display*/
            $sql = "select * from customers where customer_id='".$row_order_id["customer_id"]."'";
            $res_cus_id = mysql_query($sql);
            if ($res_cus_id)
            {
                if ($row_cus_id = mysql_fetch_assoc($res_cus_id))
                {
                    echo '<div id="customer_info_order_summary">';
                    echo '<div id="shipping_addr_past_order">';
                    echo '<span>Shipping address</span><br/>';
                    echo '<span>'.$row_cus_id["c_first_name"].' '.$row_cus_id["c_last_name"].'</span><br/>';
                    echo '<span>'.$row_cus_id["c_street_addr_shipping"].'</span><br/>';
                    echo '<span>'.$row_cus_id["c_city_shipping"].', '.$row_cus_id["c_state_shipping"].'</span><br/>';
                    echo '<span>'.$row_cus_id["c_country_shipping"].'</span><br/></div>'; //End div=shipping_addr_past_order

                    echo '<div id="payment_method_past_order">';
                    echo '<span>Credit card ending in '.substr($row_cus_id["c_credit_card"],-4).'</span><br/></div>'; //End div=payment_method_past_order

                    echo '<div id="order_summary_past_order">';
                    echo '<span style="font-weight: bold; color: orange; position: relative;">Order summary</span><br/>';
                    echo '<span>Item(s) Subtotal: </span>';
                    echo '<span class="indent_left">$'.$row_order_id["order_total_amount"].'</span><br/>';
                    echo '<span>Shipping & handling:</span>';
                    echo '<span class="indent_left">$'.$row_order_id["order_shipping_cost"].'</span><br/>';
                    echo '<span >Total before tax:</span>';
                    echo '<span class="indent_left">$'.($row_order_id["order_total_amount"]+$row_order_id["order_shipping_cost"]).'</span><br/>';
                    echo '<span>Estimated tax to be collected:</span>';
                    echo '<span class="indent_left">$0.00</span><br/>';
                    echo '<span style="font-weight: bold; color: red">Grand total:</span>';
                    echo '<span class="indent_left" style="font-weight: bold; color: red">$'.($row_order_id["order_total_amount"]+$row_order_id["order_shipping_cost"]).'</span><br/>';
                    echo '<span>Order date:</span>';
                    echo '<span class="indent_left">'.$row_order_id["order_date"].'</span></div></div>'; //End div=order_summary_past_order and div=customer_info_order_summary

                    /*Getting each product for this order*/
                    $sql = "select * from order_items where order_id='".$row_order_id["order_id"]."'";
                    $res_order_item = mysql_query($sql);
                    if ($res_order_item)
                    {
                        /*Check how many product is in this order id*/
                        $sql = "select count(product_id) as mycount from order_items where order_id='".$row_order_id["order_id"]."'";
                        $res_count = mysql_query($sql);
                        $row_count = mysql_fetch_assoc($res_count);
                        $mydiv_height = $row_count["mycount"] * 120 + 20;
                        echo '<div id="products_info" style="height: '.$mydiv_height.'px;">';
                        echo '<span style="position: absolute; left: 77%">Price</span>';
                        echo '<span style="position: relative; float: right; right: 2.5%;">Quantity</span><br/>';
                        $item_div_height = 0;
                        while ($row_order_item = mysql_fetch_assoc($res_order_item))
                        {
                            /*Need to get product info*/
                            $sql = "select * from products where product_id='".$row_order_item["product_id"]."'";
                            $res_product_id = mysql_query($sql);
                            if ($res_product_id)
                            {
                                if ($row_product_id = mysql_fetch_assoc($res_product_id))
                                {
                                    if($item_div_height == 0)
                                    {
                                        echo '<div class="shopping_cart_item_past_order">';
                                        $item_div_height += 120;
                                    }
                                    else
                                    {
                                        echo '<div class="shopping_cart_item_past_order" style="position: relative; top: '.$item_div_height.'px;">';
                                        $item_div_height += 120;
                                    }
                                    /*Need to get product info*/
                                    echo '<div class="img_product_cart">';
                                    echo '<img src="'.$row_product_id["product_image"].'" height="100px" width="100px"></div>';
                                    echo '<div class="pname_past_order">';
                                    echo '<span>'.$row_product_id["product_name"].'</span></div>';
                                    echo '<div class="price_quantity_past_order">';
                                    echo '<span style="color: red">$'.$row_order_item["p_price"].'</span>';
                                    echo '<span style="position: relative; float: right; right: 2.5%;">'.$row_order_item["order_quantity"].'</span></div></div>'; //End div=price_quantity_past_order and div=shopping_cart_item_past_order
                                }
                            }
                        }
                        echo '<button type="button" style="position: absolute; top: '.($mydiv_height).'px;" onclick=div_transform("past_orders_summary_div");>Back</button></div>'; //End div=products_info
                    }
                }
            }
        }
    }
    disconnectDB($conn);
}
?>