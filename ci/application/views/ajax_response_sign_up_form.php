<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: NguyenTran
 * Date: 7/12/2015
 * Time: 12:10 AM
 */

if (isset($username_check))
{
    echo $username_check; //response to username check if unique
}
if (isset($email_check))
{
    echo $email_check;
}

if (isset($result_add_new_user))
{
    $data_arr['from_signup'] = '1';
    if ($result_add_new_user == 'true')
    {
        #Go to log in page
        $this->load->view('log_in_form_view', $data_arr);
    }
    else
    {
        #error, back to sign up page
        $this->load->view('sign_up_form_view', $data_arr);
    }
}

?>