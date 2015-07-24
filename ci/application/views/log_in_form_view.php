<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: NguyenTran
 * Date: 7/12/2015
 * Time: 12:10 AM
 */

?>

<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="keywords" content="HTML,CSS,XML,JavaScript"/>
    <meta name="author" content="Nguyen Tran"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <script src="<?php echo base_url(); ?>javascript/jquery-1.11.3.min.js"></script>
    <script src="<?php echo base_url(); ?>javascript/main_page_js.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/main_css.css"/> <!-- link to external css file -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/login_style.css"/> <!-- link to external css file -->

    <title>N2 customer log-in</title>
</head>
<body>

<div  class="outer_box_login">
    <div class="login_form1">
        <h1 id="header_login">Please enter your credentials</h1>
        <p id="error_message_log_in_page" style="color: red">
            <?php
            if (isset($from_signup))
            {
                echo "You have successfully signed up. Do you want to log in now?";
            }
            elseif (isset($failed_log_in))
            {
                echo "Invalid login";
            }
            elseif (isset($try_edit_profile))
            {
                echo "Please log in first before you can edit your profile";
            }
            elseif (isset($time_out))
            {
                echo "Your session is timeout. Please log back in";
            }
            ?>
        </p>
        <form id="mylogin_form1" action="<?php echo base_url();?>index.php/main_webpage/log_in" method="POST">
            <div id="usr_box">
                <div id="on_side_usr">
                    <label for="usrname_login">User Name</label><span style="color: red">*</span>
                </div>
                <input type="text" id="usrname_login" name="user_name" maxlength="30" pattern="[a-zA-z0-9]+" required/>
            </div>

            <div id="pwd_box">
                <div id="on_side_pwd">
                    <label for="pwd_login">Password</label><span style="color: red">*</span>
                </div>
                <input type="password" id="pwd_login" name="pass_word" maxlength="30" required/>
            </div>

            <a href="<?php echo base_url();?>index.php/main_webpage"><button type="button" id="to_home_from_login">Home</button></a>
            <button type="submit" id="submit_log_in" name="submit_log_in" value="submit_log_in" style="position: relative; left: 10px">Submit</button>

        </form>

    </div>
</div>

</body>
</html>