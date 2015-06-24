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

        #Get user type from SESSION array
        $check_types = explode(',', $_SESSION["usertype"]);

        #Check if user log in correctly
        if (!(isset($_SESSION['username'])) || !(isset($_SESSION['password'])) || !(isset($_SESSION['usertype'])))
        {
            require "prelogin.html";
            require 'postlogin.html';
        }
        #Check if user is an admin
        elseif (!in_array("admin", $check_types))
        {
            require "prelogin.html";
            require 'postlogin.html';
        }
        #Add new role
        elseif (isset($_POST["user_id_role"]))
        {
            addNewRole();
        }
        #Add new employee
        elseif (isset($_POST["usr"]))
        {
            addNewEmployee();
        }
        #Display employee info given employee id
        elseif (isset($_POST["employee_id_modify_1"]))
        {
            display_info_for_modify();
        }
        #Change employee info based on admin's change
        elseif (isset($_POST["mysubmit_modified_form"]))
        {
            if ($_POST["modified_fname"] != '' || $_POST["modified_lname"] != '' || $_POST["modified_myaddr"] != '' || $_POST["modified_mycity"] != '' || $_POST["modified_mystate"] != '' || $_POST["modified_mycountry"] != '' || $_POST["modified_mydob"] != '' || $_POST["modified_mysalary"] != '' || isset($_POST["admin_modified_radio1"]) || isset($_POST["admin_modified_radio2"]) || $_POST["modified_myphone"] != '' || $_POST["modified_myemail"] != '' || $_POST["modified_myusername"] != '' || $_POST["modified_mypwd"] != '' || isset($_POST["admin_modified_cb1"]))
            {
                modify_employee_info();
            }
            else
            {
                require "pre_admin_page.html";
                echo '<p style="color:blue">No employee info has been changed since you did not select anything' . '</p>';
                require "post_admin_page.html";
            }
        }
        #Delete an employee from database
        elseif (isset($_POST["employee_id_delete_1"]))
        {
            delete_employee();
        }
        #For any thing else, back to Home admin page
        else
        {
            require "pre_admin_page.html";
            require "post_admin_page.html";
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
    /*validate date*/
    $dob_arr = explode('-',$mydob);
    if (!checkdate($dob_arr[1],$dob_arr[2],$dob_arr[0]))
    {
        #Wrong date
        require "pre_admin_page.html";
        echo '<p style="color:red">ERROR: The date of birth format is not correct'.'</p>';
        require "post_admin_page.html";
        return;
    }

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

            <link rel="stylesheet" type="text/css" href="admin_page_style.css"/> <!-- link to external css file -->
            <script src="admin_page_js.js"></script>
            <title>Employee info</title>
        </head>
        <body>
        <div id="admin_page_modify2">
            <h1><?php echo 'Employee '.$row["e_first_name"].' '.$row["e_last_name"].' info (employee id '.$row["employee_id"].')';
                ?></h1>

            <p id="modified_page2_errmsg" style="color:red"></p>

            <span style="font-weight: bold">Current Value</span>
            <span style="font-weight: bold; position:relative; left: 200px">Change to value</span><br/><br/>
            <form id="modified_employee_info" action="admin_page.php" method="POST">
                <!--hidden input to send server the employee id that needs to be modified -->
                <input type="hidden" name="hidden_employee_id" value="<?php echo $employee_id; ?>"/>

            <?php
            #first and last name
            echo 'Employee first name: '.$row["e_first_name"];
            echo '<input type="text" id="modified_fname" name="modified_fname" maxlength="30" pattern="\D+" style="position:absolute; left: 300px"/><br/><br/>';
            echo 'Employee last name: '.$row["e_last_name"];
            echo '<input type="text" id="modified_lname" name="modified_lname" maxlength="30" pattern="\D+" style="position:absolute; left: 300px"/><br/><br/>';

            #address info
            echo 'Street address: '.$row["e_street_addr"];
            echo '<input type="text" id="modified_myaddr" name="modified_myaddr" maxlength="50" pattern="([0-9])+\s+([A-Za-z])+.*" style="position:absolute; left: 300px"/><br/><br/>';
            echo 'City : '.$row["e_city"];
            echo '<input type="text" id="modified_mycity" name="modified_mycity" maxlength="20" pattern="\D+" style="position:absolute; left: 300px"/><br/><br/>';
            echo 'State: '.$row["e_state"];
            echo '<input type="text" id="modified_mystate" name="modified_mystate" maxlength="2" size="2" pattern="[A-Za-z]{2}" style="position:absolute; left: 300px"/><br/><br/>';
            echo 'Country: '.$row["e_country"];
            echo '<input type="text" id="modified_mycountry" name="modified_mycountry" maxlength="50" pattern="\D+" style="position:absolute; left: 300px"/><br/><br/>';

            #DOB
            echo 'Date of Birth: '.$row["e_dob"];
            echo '<input type="date" id="modified_mydob" name="modified_mydob" style="position:absolute; left: 300px"/><br/><br/>';

            #Salaray
            echo 'Salary: '.$row["e_salary"];
            echo '<input type="text" id="modified_mysalary" name="modified_mysalary" pattern ="[0-9]+" style="position:absolute; left: 300px"/><br/><br/>';

            #Marriage status
            echo 'Marriage status: '.$row["e_marriage_status"];
            echo '<input type="radio" id="admin_modified_id1" name="admin_modified_radio1" value="single" style="position:absolute; left: 300px"/><span style="position:absolute; left: 320px">Single</span>';
            echo '<input type="radio" id="admin_modified_id2" name="admin_modified_radio1" value="married" style="position:absolute; left: 400px"/><span style="position:absolute; left: 420px">Married</span>';
            echo '<input type="radio" id="admin_modified_id3" name="admin_modified_radio1" value="widow" style="position:absolute; left: 500px"/><span style="position:absolute; left: 520px">Widow</span><br/><br/>';

            #Gender
            echo 'Gender: '.$row["e_gender"];
            echo '<input type="radio" id="admin_modified_id4" name="admin_modified_radio2" value="male" style="position:absolute; left: 300px"/><span style="position:absolute; left: 320px">Male</span>';
            echo '<input type="radio" id="admin_modified_id5" name="admin_modified_radio2" value="female" style="position:absolute; left: 400px"/><span style="position:absolute; left: 420px">Female</span>';
            echo '<input type="radio" id="admin_modified_id6" name="admin_modified_radio2" value="Not declared" style="position:absolute; left: 500px"/><span style="position:absolute; left: 520px">Not declared</span><br/><br/>';

            #Phone and Email
            echo 'Phone number: '.$row["e_phone"];
            echo '<input type="tel" id="modified_myphone" name="modified_myphone" maxlength="20" pattern="[0-9]+" style="position:absolute; left: 300px"/><br/><br/>';
            echo 'Email : '.$row["e_email"];
            echo '<input type="email" id="modified_myemail" name="modified_myemail" maxlength="40" pattern="[A-Za-z0-9]+@[A-Za-z0-9]+\.[a-z]{2,3}$" style="position:absolute; left: 300px"/><br/><br/>';


            #Retrieve user name and password
            $sql = "select * from users where userid ='".$row["userid"]."'";
            $res = mysql_query($sql);

            #Check if it exists
            if (!($row = mysql_fetch_assoc($res)))
            {
                require "pre_admin_page.html";
                echo '<p style="color:red">ERROR: Cannot retrieve user name, password, and user type for employee '.$employee_id.'.This is a critical error in database. Please consult your database admin to resolve'.'</p>';
                require "post_admin_page.html";
                disconnectDB($conn);
                return;
            }

            echo 'User name: '.$row["username"];
            echo '<input type="text" id="modified_myusername" name="modified_myusername" maxlength="40" pattern="[A-Za-z0-9]+" style="position:absolute; left: 300px"/><br/><br/>';
            echo 'Password: xxxxxxx';
            echo '<input type="password" id="modified_mypwd" name="modified_mypwd" maxlength="20" pattern="[A-Za-z0-9]+" style="position:absolute; left: 300px"/><br/><br/>';
            echo 'User type: '.$row["usertype"];
            echo '<input type="checkbox" id="admin_modified_cb1" name="admin_modified_cb1[]" value="admin" style="position:absolute; left: 300px"/><span style="position:absolute; left: 320px">Admin</span>';
            echo '<input type="checkbox" id="admin_modified_cb2" name="admin_modified_cb1[]" value="manager" style="position:absolute; left: 400px"/><span style="position:absolute; left: 420px">Manager</span>';
            echo '<input type="checkbox" id="admin_modified_cb3" name="admin_modified_cb1[]" value="employee" style="position:absolute; left: 500px"/><span style="position:absolute; left: 520px">Employee</span><br/><br/>';

            echo '<button type="submit" value="go_homepage">Home</button>';
            echo '<button type="submit" onclick="return validate_modify_page2()" name="mysubmit_modified_form" value="submit_modified_form" style="position:relative; left:15px;">Submit</button>';

            ?>
            </form>
        </div>
        </body>
        </html>

    <?php
        disconnectDB($conn);
    }
    else
    {
        require "pre_admin_page.html";
        echo '<p style="color:red">ERROR: Employee id '.$employee_id.' is not an integer'.'</p>';
        require "post_admin_page.html";
    }

}

/*Function to modify info of employee*/
function modify_employee_info()
{
    /*Check if we have a correct employee id*/
    $employee_id = validate_data($_POST['hidden_employee_id']);
    if(filter_input(INPUT_POST,"hidden_employee_id",FILTER_VALIDATE_INT) && strlen($employee_id) > 0)
    {
        /*Connect to our db*/
        $conn = connectDB();
        $errmsg = "";

        /*First and Last name*/
        if($_POST['modified_fname'] != '')
        {
            $fname_element = validate_data($_POST['modified_fname']);
            $sql = "update employees set e_first_name='".$fname_element."' where employee_id='".$employee_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                #Failed to update
                $errmsg .= "Failed to update first name.\r\n";
            }
        }
        if($_POST['modified_lname'] != '')
        {
            $lname_element = validate_data($_POST['modified_lname']);
            $sql = "update employees set e_last_name='".$lname_element."' where employee_id='".$employee_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                #Failed to update
                $errmsg .= "Failed to update last name.\r\n";
            }
        }

        /*Address info*/
        if($_POST['modified_myaddr'] != '')
        {
            $myaddr = validate_data($_POST['modified_myaddr']);
            $sql = "update employees set e_street_addr='".$myaddr."' where employee_id='".$employee_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                #Failed to update
                $errmsg .= "Failed to update street address.\r\n";
            }
        }
        if($_POST['modified_mycity'] != '')
        {
            $mycity = validate_data($_POST['modified_mycity']);
            $sql = "update employees set e_city='".$mycity."' where employee_id='".$employee_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                #Failed to update
                $errmsg .= "Failed to update city.\r\n";
            }
        }
        if($_POST['modified_mystate'] != '')
        {
            $mystate = validate_data($_POST['modified_mystate']);
            $sql = "update employees set e_state='".$mystate."' where employee_id='".$employee_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                #Failed to update
                $errmsg .= "Failed to update state.\r\n";
            }
        }
        if($_POST['modified_mycountry'] != '')
        {
            $mycountry = validate_data($_POST['modified_mycountry']);
            $sql = "update employees set e_country='".$mycountry."' where employee_id='".$employee_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                #Failed to update
                $errmsg .= "Failed to update country.\r\n";
            }
        }
        /*DOB and Salary*/
        if($_POST['modified_mydob'] != '')
        {
            $mydob = validate_data($_POST['modified_mydob']);
            $sql = "update employees set e_dob='".$mydob."' where employee_id='".$employee_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                #Failed to update
            }
        }
        if($_POST['modified_mysalary'] != '')
        {
            $mysalary = validate_data($_POST['modified_mysalary']);
            $sql = "update employees set e_salary='".$mysalary."' where employee_id='".$employee_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                #Failed to update
                $errmsg .= "Failed to update salary.\r\n";
            }
        }

        /*Marriage Status and Gender*/
        if(isset($_POST['admin_modified_radio1']))
        {
            $marriage_status = validate_data($_POST['admin_modified_radio1']);
            $sql = "update employees set e_marriage_status='".$marriage_status."' where employee_id='".$employee_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                #Failed to update
                $errmsg .= "Failed to update marriage status.\r\n";
            }
        }
        if(isset($_POST['admin_modified_radio2']))
        {
            $mygender = validate_data($_POST['admin_modified_radio2']);
            $sql = "update employees set e_gender='".$mygender."' where employee_id='".$employee_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                #Failed to update
                $errmsg .= "Failed to update gender info.\r\n";
            }
        }

        /*Phone and Email*/
        if($_POST['modified_myphone'] != '')
        {
            $myphone = validate_data($_POST['modified_myphone']);
            $sql = "update employees set e_phone='".$myphone."' where employee_id='".$employee_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                #Failed to update
                $errmsg .= "Failed to update phone number.\r\n";
            }
        }
        if($_POST['modified_myemail'] != '')
        {
            $myemail = validate_data($_POST['modified_myemail']);
            $sql = "update employees set e_email='".$myemail."' where employee_id='".$employee_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                #Failed to update
                $errmsg .= "Failed to update email address.\r\n";
            }
        }

        /*User name and password*/
        if($_POST['modified_myusername'] != '')
        {
            $myusrname = validate_data($_POST['modified_myusername']);
            $sql = "select userid from employees where employee_id='".$employee_id."'";
            $res = mysql_query($sql);
            if (!($row = mysql_fetch_assoc($res)))
            {
                #Failed to update
                $errmsg .= "Fatal error: there is no user id found for this employee id ".$employee_id."\r\n";
            }
            else
            {
                $myuser_id = $row["userid"];

                #Do another sql to ensure that the username is unique
                $sql = "select * from users where username='".$myusrname."'";
                $res = mysql_query($sql);
                if (!($row = mysql_fetch_assoc($res)))
                {
                    #If nothing return, then we might have a valid username
                    $sql = "update users set username='".$myusrname."' where userid='".$myuser_id."'";
                    $res = mysql_query($sql);
                    if (!$res)
                    {
                        #Failed to update
                        $errmsg .= "Failed to update user name.\r\n";
                    }
                }
                else
                {
                    #this username has been used. Return error
                    $errmsg .= "The user name you tried to update has been used. Please try again with different username.\r\n";
                }
            }
        }
        if($_POST['modified_mypwd'] != '')
        {
            $mypwd = validate_data($_POST['modified_mypwd']);
            $sql = "select userid from employees where employee_id='".$employee_id."'";
            $res = mysql_query($sql);
            if (!($row = mysql_fetch_assoc($res)))
            {
                #Failed to update
                $errmsg .= "Fatal error: there is no user id found for this employee id ".$employee_id."\r\n";
            }
            else
            {
                $myuser_id = $row["userid"];

                $sql = "update users set password=password('".$mypwd."') where userid='".$myuser_id."'";
                $res = mysql_query($sql);
                if (!$res)
                {
                    #Failed to update
                    $errmsg .= "Failed to update password.\r\n";
                }
            }
        }

        /*User type*/
        if(isset($_POST['admin_modified_cb1']))
        {
            $usertype = validate_data_checkbox($_POST['admin_modified_cb1']);
            if ($usertype == '')
            {
                #invalid user type
                $errmsg .= "You have tried to add an invalid user type.\r\n";
            }
            else
            {
                $sql = "select userid from employees where employee_id='".$employee_id."'";
                $res = mysql_query($sql);
                if (!($row = mysql_fetch_assoc($res)))
                {
                    #Failed to update
                    $errmsg .= "Fatal error: there is no user id found for this employee id ".$employee_id."\r\n";
                }
                else
                {
                    $myuser_id = $row["userid"];

                    $sql = "update users set usertype='".$usertype."' where userid='".$myuser_id."'";
                    $res = mysql_query($sql);
                    if (!$res)
                    {
                        #Failed to update
                        $errmsg .= "Failed to update user type.\r\n";
                    }
                }
            }
        }
        disconnectDB($conn);
        if ($errmsg == '')
        {
            #No Error, woohoo!
            require "pre_admin_page.html";
            echo '<p style="color:blue">All employee info has been updated per your request!'.'</p>';
            require "post_admin_page.html";
        }
        else
        {
            require "pre_admin_page.html";
            echo '<p style="color:red">'.$errmsg.'</p>';
            require "post_admin_page.html";
        }
    }
    else
    {
        #Error employee id is not an integer
        require "pre_admin_page.html";
        echo '<p style="color:red">ERROR: Employee id '.$employee_id.' is not an integer'.'</p>';
        require "post_admin_page.html";
    }
}

/*Function to delete an employee*/
function delete_employee()
{
    #Validate the input once again
    $employee_id = validate_data($_POST['employee_id_delete_1']);
    $errmsg = "";
    if(filter_input(INPUT_POST,"employee_id_delete_1",FILTER_VALIDATE_INT) && strlen($employee_id) > 0)
    {
        $conn = connectDB();

        #Find the employee id in our database
        $sql = "select * from employees where employee_id = '" . $employee_id . "'";

        $res = mysql_query($sql);

        #Check if it exists
        if (!($row = mysql_fetch_assoc($res))) {
            require "pre_admin_page.html";
            echo '<p style="color:red">ERROR: Employee id ' . $employee_id . ' is not found in our database. Please double check your value' . '</p>';
            require "post_admin_page.html";
            disconnectDB($conn);
            return;
        }
        #save user id to delete it in users table
        $userid = $row["userid"];

        $sql = "delete from employees where employee_id = '" . $employee_id . "'";
        $res = mysql_query($sql);
        if(!$res)
        {
            #Failed to delete employee
            $errmsg .= "Failed to remove this employee id ".$employee_id."\r\n";
        }
        else
        {
            #Go on to delete the user id associate with this employee in users table
            $sql = "delete from users where userid = '".$userid."'";
            $res = mysql_query($sql);
            if(!$res)
            {
                #Failed to delete user id
                $errmsg .= "Fatal error: removed employee id ".$employee_id." from employee table, but can't remove the user id associated with this employee in users table"."\r\n";
            }
        }
        disconnectDB($conn);
        #Check for error message
        if ($errmsg == '')
        {
            #No Error, woohoo!
            require "pre_admin_page.html";
            echo '<p style="color:blue">The employee id '.$employee_id.' has been successfully removed from our database'.'</p>';
            require "post_admin_page.html";
        }
        else
        {
            require "pre_admin_page.html";
            echo '<p style="color:red">'.$errmsg.'</p>';
            require "post_admin_page.html";
        }
    }
    else
    {
        #Error employee id is not an integer
        require "pre_admin_page.html";
        echo '<p style="color:red">ERROR: Employee id '.$employee_id.' is not an integer'.'</p>';
        require "post_admin_page.html";
    }
}
?>

