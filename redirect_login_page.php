<?php
/**
 * User: NguyenTran
 * Date: 6/17/2015
 * Time: 9:34 PM
 */

if(!isset($_SESSION))
{
    session_start();
}

if (!isset($_SESSION['last_activity']) || !isset($_SESSION['usertype']) || !isset($_SESSION['username']) || !isset($_SESSION['password']) || !isset($_SESSION['timeout']))
{
    require "prelogin.html";
    require "postlogin.html";
}
else
{
    $t = time();
    if (($t - $_SESSION['last_activity']) > 1800)
    {
        $_SESSION['timeout'] = 1;
        require "logout.php";
    }
    else
    {
        #session is not yet timeout. Reset time to give users another 30 mins
        $_SESSION['last_activity'] = time();
    }

    #Get user type from SESSION array
    $check_types = explode(',', $_SESSION["usertype"]);

    #Check if user log in correctly
    if (!(isset($_SESSION['username'])) || !(isset($_SESSION['password'])) || !(isset($_SESSION['usertype'])))
    {
        require "prelogin.html";
        require 'postlogin.html';
    }
    #Check if user is an admin

    if (isset($_POST["admin_clicked"]))
    {
        #If button admin is clicked, check again to see if user really has admin type
        if (!in_array("admin", $check_types))
        {
            require "prelogin.html";
            require 'postlogin.html';
        }
        else
        {
            require "admin_page.php";
        }
    }
    elseif (isset($_POST["employee_clicked"]))
    {
        #If button admin is clicked, check again to see if user really has admin type
        if (!in_array("employee", $check_types))
        {
            require "prelogin.html";
            require 'postlogin.html';
        }
        else
        {
            require "employee_page.php";
        }
    }
    elseif (isset($_POST["manager_clicked"]))
    {
        #If button admin is clicked, check again to see if user really has admin type
        if (!in_array("manager", $check_types))
        {
            require "prelogin.html";
            require 'postlogin.html';
        }
        else
        {
            #require "admin_page.php";
        }
    }
}
?>