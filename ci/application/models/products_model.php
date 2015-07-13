<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: NguyenTran
 * Date: 7/12/2015
 * Time: 12:09 AM
 */

class Products_model extends CI_Model {

    /*Function to display detail of a product*/
    public function get_product_detail( $product_id )
    {
        /*Sanitize input*/
        $product_id = (int) $product_id;
        $sql = "select product_name, product_description, ingredients, product_image from products where product_id ='".$product_id."'";
        $res = $this->db->query($sql);
        $product_info = array();
        if ($res)
        {
            $row = $res->row_array();

            $sql = "select category_id from product_and_category where product_id = '".$product_id."'";
            $res_category = $this->db->query($sql);
            foreach ($res_category->result_array() as $category_arr)
            {
                $sql = "select category_name from product_categories where category_id = '".$category_arr["category_id"]."'";
                $res_category_name = $this->db->query($sql);
                $row_category_name = $res_category_name->row_array();
                if (! isset($product_id['category_name']))
                {
                    $product_info['category_name'] = $row_category_name["category_name"];
                }
                else
                {
                    $product_info['category_name'] .= ','.$row_category_name["category_name"];
                }
            }
            /*Storing product info*/
            $product_info['product_id'] = $product_id;
            $product_info['product_image'] = $row["product_image"];
            $product_info['product_name'] = $row["product_name"];
            $product_info['product_description'] = $row["product_description"];
            $product_info['ingredients'] = $row["ingredients"];

            /*Check if product is on sale*/
            $sql = "select special_sale_id from special_sales_and_product where product_id = '".$product_id."'";
            $res_special_sale = $this->db->query($sql);
            //Check if this product has a special sale id
            if ($res_special_sale->num_rows() > 0)
            {
                $row_special_sale = $res_special_sale->row_array();
                $sql = "select start_date, end_date, percentage_discount from special_sales where special_sale_id = '".$row_special_sale["special_sale_id"]."'";
                $res_special_sale_info = $this->db->query($sql);
                if ($res_special_sale_info->num_rows() > 0)
                {
                    $row_special_sale_info = $res_special_sale_info->row_array();
                    //load main_page_model to use check data function
                    $this->load->model('main_page_model');
                    if ($this->main_page_model->check_my_date($row_special_sale_info["start_date"], "after") && $this->main_page_model->check_my_date($row_special_sale_info["end_date"], "before"))
                    {
                        $product_info['isOnSale'] = 1;
                    }
                    else
                    {
                        $product_info['isOnSale'] = 0;
                    }
                    $product_info['start_date'] = $row_special_sale_info["start_date"];
                    $product_info['end_date'] = $row_special_sale_info["end_date"];
                    $product_info['percentage_discount'] = $row_special_sale_info["percentage_discount"];
                }
            }
            else
            {
                $product_info['isOnSale'] = 0;
            }
        }
        return $product_info;
    }
}