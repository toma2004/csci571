<?php
/**
 * User: Chris Tran
 * Date: 7/1/2015
 * Time: 6:25 PM
 */

/*If customer added a product to shopping cart, session array should have established in main_page.php*/
if(!isset($_SESSION))
{
    session_start();
}

$un = '';
$pwd = '';
$cus_id = '';
if (isset($_POST['user_name']))
{
    $un = $_POST['user_name']; #Need to validate input in php
}

if (isset($_POST['pass_word']))
{
    $pwd = $_POST['pass_word'];#Need to validate input in php
}

$errmsg = '';

if(strlen($un) == 0)
{
    $errmsg = "Invalid login";
}

if(strlen($pwd) == 0)
{
    $errmsg = "Invalid login";
}

if(strlen($un) == 0 && strlen($pwd) == 0) #First time to the page
{
    $errmsg = '';
}

#validate log in with our database
if(strlen($un) > 0 && strlen($pwd) > 0)
{
    $conn = connectDB_login();
    $sql = "select * from customers where c_username='".$un."' and c_password=password('".$pwd."')";
    $res = mysql_query($sql);

    if(!($row = mysql_fetch_assoc($res)))
    {
        $errmsg = "Invalid login";
    }
    else
    {
        $cus_id = $row["customer_id"];
    }
    #close db connection
    disconnectDB_login($conn);
}

if(strlen($errmsg) > 0)
{
    require "pre_log_in_page.html";
    echo $errmsg;
    require 'post_log_in_page.html';
}
else
{
    #store session info
    $_SESSION['username'] = $un;
    $_SESSION['password'] = $pwd;
    $_SESSION['cus_id'] = $cus_id;
    $_SESSION['last_activity'] = time();
    $_SESSION['timeout'] = 0;

    /*Check if customer already has something in the cart.
    * If yes, "merge" them with what they had last time signed in
    * If not, populate a shopping cart session array
    */
    $isShoppingCart = false;
    $isFirst = 0;
    if (isset($_SESSION["shopping_cart"]))
    {
        $isShoppingCart = true;
    }

    $conn = connectDB_login();
    /*Get customer id from customer user name*/

    $sql = "select * from shopping_cart where customer_id='".$_SESSION['cus_id']."'";
    $res_shopping_cart = mysql_query($sql);
    if ($res_shopping_cart)
    {
        while ($row_shopping_cart = mysql_fetch_assoc($res_shopping_cart))
        {
            /*Case where customer has done something with shopping cart before logging in*/
            if ($isShoppingCart)
            {
                $index = -1;
                #There is a shopping card.
                #Now check if this product already exists in the cart
                foreach ($_SESSION["shopping_cart"] as $i => $cart_items)
                {
                    if ($cart_items["pid"] == $row_shopping_cart["product_id"])
                    {
                        $index = $i;
                        break;
                    }
                }
                /*If the product does not exist, add this new product to our shopping cart array*/
                if ($index == -1)
                {
                    array_push($_SESSION["shopping_cart"], array("qty" => $row_shopping_cart["quantity"], "pid" => $row_shopping_cart["product_id"]));
                }
                else
                {
                    /*Product already exists. Update the quantity*/
                    $_SESSION["shopping_cart"][$index]["qty"] += $row_shopping_cart["quantity"];
                }
            }
            /*Case where customer has NOT done anything before logging in*/
            else
            {
                if ($isFirst == 0)
                {
                    $_SESSION["shopping_cart"][] = array("qty" => $row_shopping_cart["quantity"], "pid" => $row_shopping_cart["product_id"]);
                    $isFirst += 1;
                }
                else
                {
                    array_push($_SESSION["shopping_cart"], array("qty" => $row_shopping_cart["quantity"], "pid" => $row_shopping_cart["product_id"]));
                }
            }
        }
    }
    disconnectDB_login($conn);

    //require "main_page.php";
    require "main_webpage_logged_in.html";
}

/*Function to connect to DB*/
function connectDB_login()
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
function disconnectDB_login($myconn)
{
    mysql_close($myconn);
}