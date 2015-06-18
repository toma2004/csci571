<?php
/**
 * User: Chris Tran
 * Date: 6/12/2015
 * Time: 4:15 PM
 */
session_start();

$un = $_POST['user_name']; #Need to validate input in php
$pwd = $_POST['pass_word'];#Need to validate input in php
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
    $sql = "select usertype from users where username='".$un."' and password=password('".$pwd."')";
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
    else
    {
        $usertype = $row['usertype'];
    }
    #close db connection
    mysql_close($conn);
}

if(strlen($errmsg) > 0)
{
    require "prelogin.html";
    echo"Invalid login";
    require 'postlogin.html';
}
else if(!$res)
{
    require "prelogin.html";
    require 'postlogin.html';
}
else
{
    #store session info
    $_SESSION['username'] = $un;
    $_SESSION['password'] = $pwd;
    $_SESSION['last_activity'] = time();
    $_SESSION['timeout'] = 0;

    $_SESSION['usertype'] = $usertype;
    #check to see how many user type this employee has
    $check_types = explode(',', $_SESSION["usertype"]);
    $mycount = count($check_types);

    if($mycount == 1)
    {
        if($usertype == "admin")
        {
            require "admin_page.php";
        }
    }
    elseif ($mycount == 2 || $mycount == 3)
    {
        echo '<h1>Please select which user type you want to proceed:</h1>';
        echo '<form id="redirect_login_page" action="redirect_login_page.php" method="POST">';
        foreach ($check_types as $mytype)
        {
            if ($mytype == "admin")
            {
                echo '<button type="submit" name="admin_clicked" value="admin">Admin</button><br/><br/>';
            }
            elseif ($mytype == "employee")
            {
                echo '<button type="submit" name="employee_clicked" value="employee">Employee</button><br/><br/>';
            }
            elseif ($mytype == "manager")
            {
                echo '<button type="manager" name="manager_clicked" value="manager">Manager</button><br/><br/>';
            }
        }
        echo '</form>';

    }
    else
    {
        #error
    }
}


?>