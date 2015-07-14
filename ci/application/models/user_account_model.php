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
            $fname = $this->main_page_model->validate_data($_POST["first_name"],"first_name");
            $lname = $this->main_page_model->validate_data($_POST["last_name"],"last_name");
            $addr_shipping = $this->main_page_model->validate_data($_POST["addr_shipping"],"address");
            $city_shipping = $this->main_page_model->validate_data($_POST["city_shipping"],"city");
            $state_shipping = $this->main_page_model->validate_data($_POST["state_shipping"],"state");
            $country_shipping = $this->main_page_model->validate_data($_POST["country_shipping"],"country");
            $dob = $this->main_page_model->validate_data($_POST["dob"],"dob");
            $credit_card = $this->main_page_model->validate_data($_POST["mycreditcard"],"credit_card");
            $security_code = $this->main_page_model->validate_data($_POST["mysecuritycode"],"security_code");
            $exp_month = $this->main_page_model->validate_data($_POST["myexpiredate_month"],"exp_month");
            $exp_year = $this->main_page_model->validate_data($_POST["myexpiredate_year"],"exp_year");
            $addr_billing = $this->main_page_model->validate_data($_POST["addr_billing"],"address");
            $city_billing = $this->main_page_model->validate_data($_POST["city_billing"],"city");
            $state_billing = $this->main_page_model->validate_data($_POST["state_billing"],"state");
            $country_billing = $this->main_page_model->validate_data($_POST["country_billing"],"country");
            $phone = $this->main_page_model->validate_data($_POST["phone"],"phone");
            $email = $this->main_page_model->validate_data($_POST["email_addr"],"email");
            $username = $this->main_page_model->validate_data($_POST["usr"],"username");
            $password = $this->main_page_model->validate_data($_POST["pass"],"password");

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
}