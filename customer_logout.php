<?php
/**
 * User: Chris Tran
 * Date: 7/1/2015
 * Time: 6:38 PM
 */

if(!isset($_SESSION))
{
    session_start();
}

$errmsg = '';
if (isset($_SESSION['timeout']))
{
    if ($_SESSION['timeout'] == "1")
    {
        $errmsg .= "Your session is timeout. Please log back in";
    }
    else
    {
        $errmsg .= "You have successfully logged out";
    }
}
else
{
    $errmsg .= "You have successfully logged out";
}

/*As customer log out, we will save his/her shopping cart for next time visit*/
if (isset($_SESSION["shopping_cart"]) && isset($_SESSION["username"]))
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

    /*Before saving new shopping cart for this customer, remove the old ones*/
    $sql = "delete from shopping_cart where customer_id='".$_SESSION['cus_id']."'";
    $res_delete = mysql_query($sql);
    if ($res_delete)
    {
        /*Now insert shopping cart for this customer*/
        foreach ($_SESSION["shopping_cart"] as $cart_items)
        {
            $sql = "insert into shopping_cart values ('".$_SESSION['cus_id']."','".$cart_items["pid"]."','".$cart_items["qty"]."')";
            $res_insert = mysql_query($sql);
        }
    }

    #close db connection
    mysql_close($conn);
}


// Unset all of the session variables.
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}


session_destroy();

require "pre_main_webpage.html";
echo '<p style="color:red">'.$errmsg.'</p>';
require 'post_main_webpage.html';

?>