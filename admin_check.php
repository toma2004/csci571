<?php
/**
 * User: NguyenTran
 * Date: 6/13/2015
 * Time: 1:22 PM
 */

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
elseif (isset($_POST["user_id_role"]))
{
    addNewRole();
}
elseif (isset($_POST["usr"]))
{
    addNewEmployee();
}
else
{
    require "pre_admin_page.html";
    require "post_admin_page.html";
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

function addNewRole()
{
    #Validate the input once again
    $usr_id = validate_data($_POST['user_id_role']);
    $type = validate_data($_POST['role1_radio1']);
    if(strlen($usr_id) > 0 && strlen($type) > 0)
    {
        if($type == "admin" || $type == "manager" || $type == "employee")
        {
            $sql = "select * from users where userid='".$usr_id."'";

            $conn = connectDB();

            $res = mysql_query($sql);

            if(!($row = mysql_fetch_assoc($res)))
            {
                require "pre_admin_page.html";
                echo "Could not find userid = ".$usr_id;
                require "post_admin_page.html";
            }
            $types = explode(',', $row["usertype"]);
            if ( in_array($type, $types ))
            {
                #Try to add the same type with existing user
                require "pre_admin_page.html";
                echo "ERROR: This usertype: ".$type." is already assigned to this userid:  ".$usr_id;
                require "post_admin_page.html";
            }
            else
            {
                #$sql = "insert into users (username,password,usertype) values ('".$row["username"]."','".$row["password"]."','".$type."')";
                $temp = $row["usertype"].','."$type";
                $sql = "update users set usertype='".$temp."' where userid = '".$usr_id."'";
                $res = mysql_query($sql);
                if($res)
                {
                    require "pre_admin_page.html";
                    echo "User type ".$type." is successfully added to user_id ".$usr_id;
                    require "post_admin_page.html";
                }
            }
            disconnectDB($conn);
        }
        else
        {
            require "pre_admin_page.html";
            echo "You have tried to add an invalid type!";
            require "post_admin_page.html";
        }
    }
    else
    {
        require "pre_admin_page.html";
        echo "One of the field is empty. Please check";
        require "post_admin_page.html";
    }
}

function addNewEmployee()
{
    $fname_element = validate_data($_POST['first_name']);
    $lname_element = validate_data($_POST['last_name']);

    $myaddr = validate_data($_POST['addr']);
    $mycity = validate_data($_POST['city']);
    $mystate = validate_data($_POST['state']);
    $mycountry = validate_data($_POST['country']);

    $mydob = validate_data($_POST['dob']);

    $mysalary = validate_data($_POST['salary']);

    $marriage_status = validate_data($_POST['admin_add_radio1']);
    $mygender = validate_data($_POST['admin_add_radio2']);


    $myphone = validate_data($_POST['phone']);
    $myemail = validate_data($_POST['email_addr']);

    $user_name = validate_data($_POST['usr']);
    $pass_word = validate_data($_POST['pass']);

    $usertype = validate_data_checkbox($_POST['admin_add_checkbox1']);

    if(strlen($fname_element) > 0 && strlen($lname_element) > 0 && strlen($myaddr) > 0 && strlen($mycity) > 0 && strlen($mystate) > 0 && strlen($mycountry) > 0 && strlen($mydob) > 0 && strlen($mysalary) > 0 && strlen($marriage_status) > 0 && strlen($mygender) > 0 && strlen($myphone) > 0 && strlen($myemail) > 0 && strlen($user_name) > 0 && strlen($pass_word) > 0)
    {
        if ($usertype != '') #make sure we have a valid user type
        {
            $conn = connectDB();

            #Add this new employee to Users table first to get userid and check if username already used
            $sql = "insert into users (username,password,usertype) values ('".$user_name."','".$pass_word."','".$usertype."')";

            $res = mysql_query($sql);

            if (!$res)
            {
                require "pre_admin_page.html";
                echo "ERROR: inserting new employee. It might be due to user name has been used. User name must be unique for every employee";
                require "post_admin_page.html";
            }
            #Continue if added to users table successfully
            $sql = "select userid from users order by userid DESC limit 1";
            $res = mysql_query($sql);
            if (!($row = mysql_fetch_assoc($res)))
            {
                require "pre_admin_page.html";
                echo "ERROR: inserting new employee";
                require "post_admin_page.html";
            }

            #Now add to employee table
            $sql = "insert into employees (userid,e_first_name,e_last_name,e_street_addr,e_city,e_state,e_country,e_marriage_status,e_gender,e_dob,e_phone,e_salary,e_email) values ('".$row["userid"]."','".$fname_element."','".$lname_element."','".$myaddr."','".$mycity."','".$mystate."','".$mycountry."','".$marriage_status."','".$mygender."','".$mydob."','".$myphone."','".$mysalary."','".$myemail."')";
            $res = mysql_query($sql);
            if (!$res)
            {
                require "pre_admin_page.html";
                echo "ERROR: inserting new employee.";
                require "post_admin_page.html";
            }

            disconnectDB($conn);
        }
        else
        {
            require "pre_admin_page.html";
            echo "You have tried to add an invalid type!";
            require "post_admin_page.html";
        }
    }
    else
    {
        require "pre_admin_page.html";
        echo "One of the field is empty. Please check";
        require "post_admin_page.html";
    }
}

/*Function to validate form data*/
function validate_data($data)
{
    $data = trim($data); //remove whitespaces
    $data = stripslashes($data); //remove all backslashes
    $data = htmlspecialchars($data);
    return $data;
}

/*Function to validate form data of checkbox where multiple values are allowed*/
function validate_data_checkbox($data_arr)
{
    $i = 0;
    if (is_array($data_arr))
    {
        foreach ($data_arr as $val)
        {
            $val = trim($val); //remove whitespaces
            $val = stripslashes($val); //remove all backslashes
            $val = htmlspecialchars($val);
            if ($val != "admin" && $val != "manager" && $val != "employee") #invalid user type
            {
                return '';
            }
            $arr[$i] = $val;
            $i++;
        }
    }
    return implode(',',$arr);
}

?>

