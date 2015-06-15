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
else
{
    ?>
    <!DOCTYPE html>
    <html>
    <head lang="en">
        <meta charset="UTF-8">
        <meta name="keywords" content="HTML,CSS,XML,JavaScript"/>
        <meta name="author" content="Nguyen Tran"/>
        <meta http-equiv="refresh" content="1800"/> <!--refresh page every 30 mins -->

        <script src="admin_page_js.js"></script>
        <title>N2 internal admin page</title>
    </head>
    <body>

    <!-- Home page of admin page -->
    <div id="admin_page_form1">
        <h1>Hello admin!</h1>
        <p>Please select action you want to perform below:</p>

        <form id="myadmin_page_form1" action="logout.php">
            <button type="button" onclick="admin_transform('admin_page_form1','admin_page_add1')">Add</button>
            <button type="button">Modify</button>
            <button type="button">Delete</button>
            <button type="submit">Logout</button>
        </form>
    </div>

    <!-- Add new employee or new role to an existing employee page -->
    <div id="admin_page_add1" style="display:none;">
        <h1>Add more user</h1>
        <p id="err_msg"></p>
        <form id="myadmin_page_add1">
            <input type="radio" id="add1_id1" name="add1_radio1" value="newrole"/>New Role<br/>
            <input type="radio" id="add1_id2" name="add1_radio1" value="newemployee"/>New Employee<br/>

            <button type="button" onclick="admin_transform('admin_page_add1','admin_page_form1')">Back</button>
            <button type="button" onclick="validate_add1_transform()">Continue</button>
        </form>
    </div>

    <!-- New Role add page -->
    <div id="admin_page_add2_role" style="display:none;">
        <h1>Add new role</h1>
        <p id="err_msg_add_role" style="color:red"></p>

        <form id="admin_page_add_role" action="add_new_role.php" method="POST">

            <p>Please type in user id that you want to add new role (numbers only)</p>
            <label for="usr_id_add_role">User id</label>
            <input type="text" id="usr_id_add_role" name="user_id_role" pattern="\d+" required/><br/> <!--need validate -->

            <p>What role do you want to add to this user id?</p>
            <input type="radio" id="role1_id1" name="role1_radio1" value="admin"/>admin<br/>
            <input type="radio" id="role1_id2" name="role1_radio1" value="manager"/>manager<br/>
            <input type="radio" id="role1_id3" name="role1_radio1" value="employee"/>employee<br/>

            <button type="button" onclick="admin_transform('admin_page_add2_role','admin_page_add1')">Back</button>
            <button type="button" onclick="admin_transform('admin_page_add2_role','admin_page_form1')">Home</button>
            <button type="submit" onclick="return validate_add_role_page()">Submit</button>

        </form>

    </div>

    <!-- New Employee add page -->
    <div id="admin_page_add2_employee" style="display:none;">
        <h1>Add new employee</h1>
        <p id="err_msg_add_employee"></p>

        <form id="admin_page_add_employee">

            <button type="button" onclick="admin_transform('admin_page_add2_employee','admin_page_add1')">Back</button>
            <button type="button" onclick="admin_transform('admin_page_add2_employee','admin_page_form1')">Home</button>

        </form>

    </div>

    </body>
    </html>
<?php
}

?>

