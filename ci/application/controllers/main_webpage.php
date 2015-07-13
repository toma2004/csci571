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
        //Need to get product id from POST
        //echo $_POST['product_name_clicked'];
        //Loadl model and get data
        $this->load->model('products_model');
        $data_detail['detail_product_info'] = $this->products_model->get_product_detail($this->input->post('product_name_clicked'));
        $this->load->view('product_detail_view', $data_detail);
    }

    /*Function to response from either Home button or Add_To_Cart button*/
    public function response_product_detail_page()
    {
        if ($this->input->post('to_home') != NULL)
        {
            $this->index();
        }
    }

    /*Function to display all products under a category*/
    public function display_products_in_category()
    {
        $this->load->model('products_model');
        echo $this->input->post('display_category_id');
    }
}