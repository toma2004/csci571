<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: NguyenTran
 * Date: 7/12/2015
 * Time: 12:09 AM
 */

class Main_webpage extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('html_helper');
        $this->load->helper('url');
    }

    /*Default function to display main webpage when customer connects to the site*/
    public function index()
    {
        //Load model and get data
        $this->load->model('main_page_model');
        $data_to_main_page_view['special_sale_display'] = $this->main_page_model->get_special_sale_display();
        $data_to_main_page_view['category_list'] = $this->main_page_model->get_category_dropDown_list();
        //Load view
        $this->load->view('main_page_view', $data_to_main_page_view);
    }

    /*Function to get detail of a product*/
    public function get_product_detail()
    {
        //Load model and get data
        $this->load->model('products_model');
        $data_detail['detail_product_info'] = $this->products_model->get_product_detail($this->input->post('product_name_clicked'));
        $this->load->view('product_detail_view', $data_detail);
    }

    /*Function to response from Add_To_Cart button*/
    public function response_product_detail_page()
    {
        //need to handle add to cart
    }

    /*Function to display all products under a category*/
    public function display_products_in_category()
    {
        $this->load->model('products_model');
        $data_detail['products_in_category'] = $this->products_model->get_products_in_category($this->input->post('display_category_id'));
        $this->load->view('products_in_category_div', $data_detail); //This view is an AJAX response
    }

    /*Function to handle sign up process*/
    public function user_sign_up()
    {
        $this->load->model('user_account_model');
        $data_pass_to_view = array();
        if ($this->input->post('sign_up_user_name') != NULL)
        {
            //AJAX request to check if user name is OK with no duplicate in our database
            $data_pass_to_view['username_check'] = $this->user_account_model->checkUnique($this->input->post('sign_up_user_name'), 'user_name');
        }
        if ($this->input->post('sign_up_email') != NULL)
        {
            //AJAX request to check if user name is OK with no duplicate in our database
            $data_pass_to_view['email_check'] = $this->user_account_model->checkUnique($this->input->post('sign_up_email'), 'email');
        }
        if ($this->input->post('submit_sign_up_form') != NULL)
        {
            $data_pass_to_view['result_add_new_user'] = $this->user_account_model->add_new_customer($this->input->post('first_name'), $this->input->post('last_name'),$this->input->post('addr_shipping'),$this->input->post('city_shipping'),$this->input->post('state_shipping'),$this->input->post('country_shipping'),$this->input->post('dob'),$this->input->post('mycreditcard'),$this->input->post('mysecuritycode'),$this->input->post('myexpiredate_month'),$this->input->post('myexpiredate_year'),$this->input->post('addr_billing'),$this->input->post('city_billing'),$this->input->post('state_billing'), $this->input->post('country_billing'), $this->input->post('phone'), $this->input->post('email_addr'), $this->input->post('usr'), $this->input->post('pass'));
        }
        $this->load->view('ajax_response_sign_up_form',$data_pass_to_view);
    }

    /*Function to handle log in*/
    public function log_in()
    {

    }
}