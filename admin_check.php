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

$t = time();
if (($t - $_SESSION['last_activity']) > 1800)
{
    require "prelogin.html";
    echo 'Your session is timeout. Please log back in';
    require 'postlogin.html';
}
else
{
    #session is not yet timeout. Reset time to give users another 30 mins
    $_SESSION['last_activity'] = time();
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
elseif (isset($_POST["employee_id_modify_1"]))
{
    display_info_for_modify();
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

/*Function to add new role to an existing userid*/
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
                echo '<p style="color:red">ERROR: Could not find userid '.$usr_id.'</p>';
                require "post_admin_page.html";
                disconnectDB($conn);
                return;
            }
            $types = explode(',', $row["usertype"]);
            if ( in_array($type, $types ))
            {
                #Try to add the same type with existing user
                require "pre_admin_page.html";
                echo '<p style="color:red">ERROR: This usertype: '.$type.' is already assigned to this userid:  '.$usr_id.'</p>';
                require "post_admin_page.html";
                disconnectDB($conn);
                return;
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
                    echo '<p style="color:blue">User type '.$type.' is successfully added to user_id '.$usr_id.'</p>';
                    require "post_admin_page.html";
                }
            }
            disconnectDB($conn);
        }
        else
        {
            require "pre_admin_page.html";
            echo '<p style="color:red">ERROR: You have tried to add an invalid type!'.'</p>';
            require "post_admin_page.html";
        }
    }
    else
    {
        require "pre_admin_page.html";
        echo '<p style="color:red">ERROR: One of the field is empty. Please check'.'</p>';
        require "post_admin_page.html";
    }
}

/*Function to add new employee*/
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
            $sql = "insert into users (username,password,usertype) values ('".$user_name."',password('".$pass_word."'),'".$usertype."')";

            $res = mysql_query($sql);

            if (!$res)
            {
                require "pre_admin_page.html";
                echo '<p style="color:red">ERROR: inserting new employee. It might be due to user name has been used. User name must be unique for every employee'.'</p>';
                require "post_admin_page.html";
                disconnectDB($conn);
                return;
            }
            #Continue if added to users table successfully
            $sql = "select userid from users order by userid DESC limit 1";
            $res = mysql_query($sql);
            if (!($row = mysql_fetch_assoc($res)))
            {
                require "pre_admin_page.html";
                echo '<p style="color:red">ERROR: inserting new employee'.'</p>';
                require "post_admin_page.html";
                disconnectDB($conn);
                return;
            }

            #Now add to employee table
            $sql = "insert into employees (userid,e_first_name,e_last_name,e_street_addr,e_city,e_state,e_country,e_marriage_status,e_gender,e_dob,e_phone,e_salary,e_email) values ('".$row["userid"]."','".$fname_element."','".$lname_element."','".$myaddr."','".$mycity."','".$mystate."','".$mycountry."','".$marriage_status."','".$mygender."','".$mydob."','".$myphone."','".$mysalary."','".$myemail."')";
            $res = mysql_query($sql);
            if (!$res)
            {
                require "pre_admin_page.html";
                echo '<p style="color:red">ERROR: inserting new employee.'.'</p>';
                require "post_admin_page.html";
            }
            else
            {
                require "pre_admin_page.html";
                echo '<p style="color:blue">Employee '.$fname_element.' '.$lname_element.' is added successfully to our database'.'</p>';
                require "post_admin_page.html";
            }

            disconnectDB($conn);
        }
        else
        {
            require "pre_admin_page.html";
            echo '<p style="color:red">You have tried to add an invalid type!'.'</p>';
            require "post_admin_page.html";
        }
    }
    else
    {
        require "pre_admin_page.html";
        echo '<p style="color:red">One of the field is empty. Please check'.'</p>';
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

function display_info_for_modify()
{
    #Validate the input once again
    $employee_id = validate_data($_POST['employee_id_modify_1']);
    if(filter_input(INPUT_POST,"employee_id_modify_1",FILTER_VALIDATE_INT) && strlen($employee_id) > 0)
    {
        $conn = connectDB();

        #Find the employee id in our database
        $sql = "select * from employees where employee_id = '".$employee_id."'";

        $res = mysql_query($sql);

        #Check if it exists
        if (!($row = mysql_fetch_assoc($res)))
        {
            require "pre_admin_page.html";
            echo '<p style="color:red">ERROR: Employee id '.$employee_id.' is not found in our database. Please double check your value'.'</p>';
            require "post_admin_page.html";
            disconnectDB($conn);
            return;
        }
        ?>
        <!-- End php and display html -->
        <!DOCTYPE html>
        <html>
        <head lang="en">
            <meta charset="UTF-8"/>
            <meta name="author" content="Nguyen Tran"/>

           <!-- <link rel="stylesheet" type="text/css" href="main_css.css"/> <!-- link to external css file -->
            <!--<link rel="shortcut icon" type="image/jpg" href="flower_crown1.jpg"></link> -->
            <script src="admin_page_js.js"></script>
            <title>Employee info</title>
        </head>
        <body>
            <h1><?php echo 'Employee '.$row["e_first_name"].' '.$row["e_last_name"].' info (employee id '.$row["employee_id"].')';
                ?></h1>
            <!-- need to create div here -->
            <button type="button" onclick="admin_transform('admin_page_modify1','admin_page_form1')">Home</button>
        </body>

    <?php
        disconnectDB($conn);
    }
    else
    {
        require "pre_admin_page.html";
        echo '<p style="color:red">ERROR: Employee id '.$employee_id.' is not found in not an integer'.'</p>';
        require "post_admin_page.html";
    }

}
?>

