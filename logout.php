<?php
/**
 * User: NguyenTran
 * Date: 6/13/2015
 * Time: 2:03 PM
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

require "prelogin.html";
echo '<p style="color:red">'.$errmsg.'</p>';
require 'postlogin.html';

?>