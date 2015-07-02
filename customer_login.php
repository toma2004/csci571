<?php
/**
 * User: Chris Tran
 * Date: 7/1/2015
 * Time: 6:25 PM
 */

session_start();

$un = '';
$pwd = '';
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
    $sql = "select * from customers where c_username='".$un."' and c_password=password('".$pwd."')";
    $host = 'localhost';
    $user = 'root';
    $pass = 'ntcsci571hw2';

    $conn = mysql_connect($host, $user, $pass);
    if (!$conn)
    {
        die("Could not connect to database");
    }
    mysql_select_db('n2_internal_db',$conn);

    $res = mysql_query($sql);

    if(!($row = mysql_fetch_assoc($res)))
    {
        $errmsg = "Invalid login";
    }
    #close db connection
    mysql_close($conn);
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
    $_SESSION['last_activity'] = time();
    $_SESSION['timeout'] = 0;
}