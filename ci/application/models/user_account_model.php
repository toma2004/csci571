<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: NguyenTran
 * Date: 7/12/2015
 * Time: 12:09 AM
 */

class User_account_model extends CI_Model {

    /*Constructor*/
    public function __construct()
    {
        parent::__construct();
        //load main_page_model to use functions in that class
        $this->load->model('main_page_model');
    }

    /*Function to check if a value is unique in a specified column in my database*/
    public function checkUnique( $val, $col )
    {
        //sanitize user's data
        $val = $this->main_page_model->validate_data($val, 'username');
        if ($val != false)
        {
            if ($col == 'user_name')
            {
                $sql = "select * from customers where c_username=?";
            }
            elseif ($col == 'email')
            {
                $sql = "select * from customers where c_email=?";
            }
            $res = $this->db->query($sql, array($val));
            if ($res->num_rows() == 0)
            {
                return 'true';
            }
            else
            {
                return 'false';
            }
        }
        return 'false';
    }

    /*Function to add new user to our database*/
    public function add_new_customer ($fname,$lname,$addr_shipping,$city_shipping,$state_shipping,$country_shipping,$dob,$credit_card,$security_code,$exp_month,$exp_year,$addr_billing,$city_billing,$state_billing,$country_billing,$phone,$email,$username,$password)
    {
        #check if we are missing any value
        if ($fname != NULL && $lname != NULL && $addr_shipping != NULL && $city_shipping != NULL && $state_shipping != NULL && $country_shipping != NULL && $dob != NULL && $credit_card != NULL && $security_code != NULL && $exp_month != NULL && $exp_year != NULL && $addr_billing != NULL && $city_billing != NULL && $state_billing != NULL && $country_billing != NULL && $phone != NULL && $email != NULL && $username != NULL && $password != NULL)
        {
            //Do validation
            $fname = $this->main_page_model->validate_data($fname,"first_name");
            $lname = $this->main_page_model->validate_data($lname,"last_name");
            $addr_shipping = $this->main_page_model->validate_data($addr_shipping,"address");
            $city_shipping = $this->main_page_model->validate_data($city_shipping,"city");
            $state_shipping = $this->main_page_model->validate_data($state_shipping,"state");
            $country_shipping = $this->main_page_model->validate_data($country_shipping,"country");
            $dob = $this->main_page_model->validate_data($dob,"dob");
            $credit_card = $this->main_page_model->validate_data($credit_card,"credit_card");
            $security_code = $this->main_page_model->validate_data($security_code,"security_code");
            $exp_month = $this->main_page_model->validate_data($exp_month,"exp_month");
            $exp_year = $this->main_page_model->validate_data($exp_year,"exp_year");
            $addr_billing = $this->main_page_model->validate_data($addr_billing,"address");
            $city_billing = $this->main_page_model->validate_data($city_billing,"city");
            $state_billing = $this->main_page_model->validate_data($state_billing,"state");
            $country_billing = $this->main_page_model->validate_data($country_billing,"country");
            $phone = $this->main_page_model->validate_data($phone,"phone");
            $email = $this->main_page_model->validate_data($email,"email");
            $username = $this->main_page_model->validate_data($username,"username");
            $password = $this->main_page_model->validate_data($password,"password");

            /*validate date*/
            $dob_arr = explode('-',$dob);

            if ($fname == false || $lname == false || $addr_shipping == false || $city_shipping == false || $state_shipping == false || $country_shipping == false || $dob == false || $credit_card == false || $security_code == false || $exp_month == false || $exp_year == false || $addr_billing == false || $city_billing == false || $state_billing == false || $country_billing == false || $phone == false || $email == false || $username == false || $password == false)
            {
                return 'false';
            }
            elseif (!checkdate($dob_arr[1],$dob_arr[2],$dob_arr[0]))
            {
                return 'false';
            }
            else
            {
                $sql = "insert into customers (c_first_name, c_last_name, c_street_addr_shipping, c_city_shipping, c_state_shipping, c_country_shipping, c_dob, c_credit_card, c_security_code, c_exp_month, c_exp_year, c_street_addr_billing, c_city_billing, c_state_billing, c_country_billing, c_phone, c_email, c_username, c_password) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,password(?))";
                $res = $this->db->query($sql, array($fname,$lname,$addr_shipping,$city_shipping,$state_shipping,$country_shipping,$dob,$credit_card,$security_code,$exp_month,$exp_year,$addr_billing,$city_billing,$state_billing,$country_billing,$phone,$email,$username,$password));
                if ($res)
                {
                    return 'true';
                }
                else
                {
                    return 'false';
                }
            }
        }
        else
        {
            return 'false';
        }
    }

    /*Function to check if user's credential is correct to log in*/
    public function check_log_in( $usr,$pass )
    {
        /*Validate user's input*/
        $usr = $this->main_page_model->validate_data($usr, "username");
        $pass = $this->main_page_model->validate_data($pass, "password");
        if ($usr == false || $pass == false)
        {
            return 'false';
        }
        else
        {
            $sql = "select * from customers where c_username=? and c_password=password(?)";
            $res = $this->db->query($sql, array($usr,$pass));
            if ($res->num_rows() > 0)
            {
                $row = $res->row_array();
                return $row["customer_id"];
            }
            else
            {
                return 'false';
            }
        }
    }

    /*Function to get customer info to edit*/
    public function get_customer_info( $cus_id )
    {
        $cus_id = $this->main_page_model->validate_data($cus_id, "int");
        if ($cus_id == false)
        {
            return 'false';
        }
        else
        {
            $sql = "select * from customers where customer_id=?";
            $res = $this->db->query($sql, $cus_id);
            if ($res->num_rows() > 0)
            {
                $row = $res->row_array();
                return array(
                    'c_first_name' => $row['c_first_name'],
                    'c_last_name' => $row['c_last_name'],
                    'c_street_addr_shipping' => $row['c_street_addr_shipping'],
                    'c_city_shipping' => $row['c_city_shipping'],
                    'c_state_shipping' => $row['c_state_shipping'],
                    'c_country_shipping' => $row['c_country_shipping'],
                    'c_dob' => $row['c_dob'],
                    'c_credit_card' => $row['c_credit_card'],
                    'c_security_code' => $row['c_security_code'],
                    'c_exp_month' => $row['c_exp_month'],
                    'c_exp_year' => $row['c_exp_year'],
                    'c_street_addr_billing' => $row['c_street_addr_billing'],
                    'c_city_billing' => $row['c_city_billing'],
                    'c_state_billing' => $row['c_state_billing'],
                    'c_country_billing' => $row['c_country_billing'],
                    'c_phone' => $row['c_phone'],
                    'c_email' => $row['c_email']
                );
            }
            else
            {
                return 'false';
            }
        }
    }

    /*Function to update database based on customer's profile edit*/
    public function customer_edit_profile($cus_id,$fname,$lname,$addr_shipping,$city_shipping,$state_shipping,$country_shipping,$dob,$credit_card,$security_code,$exp_month,$exp_year,$addr_billing,$city_billing,$state_billing,$country_billing,$phone,$email,$password)
    {
        $cus_id = $this->main_page_model->validate_data($cus_id, "int");
        if ($cus_id == false)
        {
            return 'false';
        }
        else
        {
            $err_msg = '';
            if ($fname != NULL)
            {
                $fname = $this->main_page_model->validate_data($fname,"first_name");
                if ($fname == false)
                {
                    $err_msg .= "First name is not in a right format\r\n";
                }
                else
                {
                    $sql = "update customers set c_first_name=? where customer_id=?";
                    $res = $this->db->query($sql, array($fname,$cus_id));
                    if (!$res)
                    {
                        $err_msg .= "ERROR: updating first name of customer's id ".$cus_id."\r\n";
                    }
                }
            }
            if ($lname != NULL)
            {
                $lname = $this->main_page_model->validate_data($lname,"last_name");
                if ($lname == false)
                {
                    $err_msg .= "Last name is not in a right format\r\n";
                }
                else
                {
                    $sql = "update customers set c_last_name=? where customer_id=?";
                    $res = $this->db->query($sql, array($lname,$cus_id));
                    if (!$res)
                    {
                        $err_msg .= "ERROR: updating last name of customer's id ".$cus_id."\r\n";
                    }
                }
            }
            if ($addr_shipping != NULL)
            {
                $addr_shipping = $this->main_page_model->validate_data($addr_shipping,"address");
                if ($addr_shipping == false)
                {
                    $err_msg .= "Shipping street address is not in a right format\r\n";
                }
                else
                {
                    $sql = "update customers set c_street_addr_shipping=? where customer_id=?";
                    $res = $this->db->query($sql, array($addr_shipping,$cus_id));
                    if (!$res)
                    {
                        $err_msg .= "ERROR: updating Shipping street address of customer's id ".$cus_id."\r\n";
                    }
                }
            }
            if ($city_shipping != NULL)
            {
                $city_shipping = $this->main_page_model->validate_data($city_shipping,"city");
                if ($city_shipping == false)
                {
                    $err_msg .= "Shipping city is not in a right format\r\n";
                }
                else
                {
                    $sql = "update customers set c_city_shipping=? where customer_id=?";
                    $res = $this->db->query($sql, array($city_shipping,$cus_id));
                    if (!$res)
                    {
                        $err_msg .= "ERROR: updating Shipping city of customer's id ".$cus_id."\r\n";
                    }
                }
            }
            if ($state_shipping != NULL)
            {
                $state_shipping = $this->main_page_model->validate_data($state_shipping,"state");
                if ($state_shipping == false)
                {
                    $err_msg .= "Shipping state is not in a right format\r\n";
                }
                else
                {
                    $sql = "update customers set c_state_shipping=? where customer_id=?";
                    $res = $this->db->query($sql, array($state_shipping,$cus_id));
                    if (!$res)
                    {
                        $err_msg .= "ERROR: updating Shipping state of customer's id ".$cus_id."\r\n";
                    }
                }
            }
            if ($country_shipping != NULL)
            {
                $country_shipping = $this->main_page_model->validate_data($country_shipping,"country");
                if ($country_shipping == false)
                {
                    $err_msg .= "Shipping country is not in a right format\r\n";
                }
                else
                {
                    $sql = "update customers set c_country_shipping=? where customer_id=?";
                    $res = $this->db->query($sql, array($country_shipping,$cus_id));
                    if (!$res)
                    {
                        $err_msg .= "ERROR: updating Shipping country of customer's id ".$cus_id."\r\n";
                    }
                }
            }
            if ($dob != NULL)
            {
                $dob = $this->main_page_model->validate_data($dob,"dob");
                if ($dob == false)
                {
                    $err_msg .= "Date of Birth is not in a right format or it is later than today date\r\n";
                }
                else
                {
                    $sql = "update customers set c_dob=? where customer_id=?";
                    $res = $this->db->query($sql, array($dob,$cus_id));
                    if (!$res)
                    {
                        $err_msg .= "ERROR: updating Date of Birth of customer's id ".$cus_id."\r\n";
                    }
                }
            }
            if ($credit_card != NULL)
            {
                $credit_card = $this->main_page_model->validate_data($credit_card,"credit_card");
                if ($credit_card == false)
                {
                    $err_msg .= "Credit card number is not in a right format\r\n";
                }
                else
                {
                    $sql = "update customers set c_credit_card=? where customer_id=?";
                    $res = $this->db->query($sql, array($credit_card,$cus_id));
                    if (!$res)
                    {
                        $err_msg .= "ERROR: updating Credit card number of customer's id ".$cus_id."\r\n";
                    }
                }
            }
            if ($security_code != NULL)
            {
                $security_code = $this->main_page_model->validate_data($security_code,"security_code");
                if ($security_code == false)
                {
                    $err_msg .= "Security code number is not in a right format\r\n";
                }
                else
                {
                    $sql = "update customers set c_security_code=? where customer_id=?";
                    $res = $this->db->query($sql, array($security_code,$cus_id));
                    if (!$res)
                    {
                        $err_msg .= "ERROR: updating Security code number of customer's id ".$cus_id."\r\n";
                    }
                }
            }
            if ($exp_month != NULL)
            {
                $exp_month = $this->main_page_model->validate_data($exp_month,"exp_month");
                if ($exp_month == false)
                {
                    $err_msg .= "Expiration month is not in a right format\r\n";
                }
                else
                {
                    $sql = "update customers set c_exp_month=? where customer_id=?";
                    $res = $this->db->query($sql, array($exp_month,$cus_id));
                    if (!$res)
                    {
                        $err_msg .= "ERROR: updating Expiration month of customer's id ".$cus_id."\r\n";
                    }
                }
            }
            if ($exp_year != NULL)
            {
                $exp_year = $this->main_page_model->validate_data($exp_year,"exp_year");
                if ($exp_year == false)
                {
                    $err_msg .= "Expiration year is not in a right format\r\n";
                }
                else
                {
                    $sql = "update customers set c_exp_year=? where customer_id=?";
                    $res = $this->db->query($sql, array($exp_year,$cus_id));
                    if (!$res)
                    {
                        $err_msg .= "ERROR: updating Expiration year of customer's id ".$cus_id."\r\n";
                    }
                }
            }
            if ($addr_billing != NULL)
            {
                $addr_billing = $this->main_page_model->validate_data($addr_billing,"address");
                if ($addr_billing == false)
                {
                    $err_msg .= "Billing address is not in a right format\r\n";
                }
                else
                {
                    $sql = "update customers set c_street_addr_billing=? where customer_id=?";
                    $res = $this->db->query($sql, array($addr_billing,$cus_id));
                    if (!$res)
                    {
                        $err_msg .= "ERROR: updating billing address of customer's id ".$cus_id."\r\n";
                    }
                }
            }
            if ($city_billing != NULL)
            {
                $city_billing = $this->main_page_model->validate_data($city_billing,"city");
                if ($city_billing == false)
                {
                    $err_msg .= "Billing city is not in a right format\r\n";
                }
                else
                {
                    $sql = "update customers set c_city_billing=? where customer_id=?";
                    $res = $this->db->query($sql, array($city_billing,$cus_id));
                    if (!$res)
                    {
                        $err_msg .= "ERROR: updating billing city of customer's id ".$cus_id."\r\n";
                    }
                }
            }
            if ($state_billing != NULL)
            {
                $state_billing = $this->main_page_model->validate_data($state_billing,"state");
                if ($state_billing == false)
                {
                    $err_msg .= "Billing state is not in a right format\r\n";
                }
                else
                {
                    $sql = "update customers set c_state_billing=? where customer_id=?";
                    $res = $this->db->query($sql, array($state_billing,$cus_id));
                    if (!$res)
                    {
                        $err_msg .= "ERROR: updating billing state of customer's id ".$cus_id."\r\n";
                    }
                }
            }
            if ($country_billing != NULL)
            {
                $country_billing = $this->main_page_model->validate_data($country_billing,"country");
                if ($country_billing == false)
                {
                    $err_msg .= "Billing country is not in a right format\r\n";
                }
                else
                {
                    $sql = "update customers set c_country_billing=? where customer_id=?";
                    $res = $this->db->query($sql, array($country_billing,$cus_id));
                    if (!$res)
                    {
                        $err_msg .= "ERROR: updating billing country of customer's id ".$cus_id."\r\n";
                    }
                }
            }
            if ($phone != NULL)
            {
                $phone = $this->main_page_model->validate_data($phone,"phone");
                if ($phone == false)
                {
                    $err_msg .= "Phone number is not in a right format\r\n";
                }
                else
                {
                    $sql = "update customers set c_phone=? where customer_id=?";
                    $res = $this->db->query($sql, array($phone,$cus_id));
                    if (!$res)
                    {
                        $err_msg .= "ERROR: updating Phone number of customer's id ".$cus_id."\r\n";
                    }
                }
            }
            if ($email != NULL)
            {
                $email = $this->main_page_model->validate_data($email,"email");
                if ($email == false)
                {
                    $err_msg .= "Email address is not in a right format\r\n";
                }
                else
                {
                    $sql = "update customers set c_email=? where customer_id=?";
                    $res = $this->db->query($sql, array($email,$cus_id));
                    if (!$res)
                    {
                        $err_msg .= "ERROR: updating email address of customer's id ".$cus_id."\r\n";
                    }
                }
            }
            if ($password != NULL)
            {
                $password = $this->main_page_model->validate_data($password,"password");
                if ($password == false)
                {
                    $err_msg .= "Password is not in a right format\r\n";
                }
                else
                {
                    $sql_pass = "update customers set c_password=password(?) where customer_id=?";
                    $res = $this->db->query($sql_pass, array($password,$cus_id));
                    if (!$res)
                    {
                        $err_msg .= "ERROR: updating password of customer's id ".$cus_id."\r\n";
                    }
                }
            }
            if (strlen($err_msg) > 0)
            {
                return $err_msg;
            }
            else
            {
                return 'true';
            }
        }
    }
}