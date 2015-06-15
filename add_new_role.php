<?php
/**
 * User: NguyenTran
 * Date: 6/13/2015
 * Time: 3:35 PM
 */
echo "WIll add new role here";
if(!isset($_SESSION))
{
    session_start();
}

if(!(isset($_SESSION['username'])) || !(isset($_SESSION['password'])) || !(isset($_SESSION['usertype'])))
{
    require "prelogin.html";
    require 'postlogin.html';
}
elseif ($_SESSION['usertype'] != "admin")
{
    require "prelogin.html";
    require 'postlogin.html';
}
else
{
    #Validate the input once again
    $usr_id = $_POST['user_id_role'];
    $type = $_POST['role1_radio1'];
    $errmsg = '';
    if(strlen($usr_id) > 0 && strlen($type) > 0)
    {
        if($type == "admin" || $type == "manager" || $type == "employee")
        {
            $host = 'localhost';
            $user = 'root';
            $pass = 'ntcsci571hw2';

            $sql = "select * from users where userid='".$usr_id."'";

            $conn = mysql_connect($host, $user, $pass);
            if (!$conn)
            {
                die("Could not connect to database");
            }
            mysql_select_db('n2_internal_db',$conn);

            $res = mysql_query($sql);

            if(!($row = mysql_fetch_assoc($res)))
            {
                $errmsg = "Could not find userid = ".$usr_id;
            }
            elseif ($type == $row["usertype"])
            {
                #Try to add the same type with existing user
                $errmsg = "ERROR: This usertype: ".$type." is already assigned to this userid:  ".$usr_id;
            }
            else
            {
                $sql = "insert into users (username,password,usertype) values ('".$row["username"]."','".$row["password"]."','".$type."')";

                $res = mysql_query($sql);
                if($res)
                {
                    echo "INSERT SUCCESS";
                }
            }
            mysql_close($conn);
        }
    }
}
?>