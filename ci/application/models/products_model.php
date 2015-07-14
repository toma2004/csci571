<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: NguyenTran
 * Date: 7/12/2015
 * Time: 12:09 AM
 */

class Products_model extends CI_Model {

    /*Constructor*/
    public function __construct()
    {
        parent::__construct();
        //load main_page_model to use functions in that class
        $this->load->model('main_page_model');
    }

    /*Function to display detail of a product*/
    public function get_product_detail( $product_id )
    {
        /*Sanitize input*/
        $product_id = $this->main_page_model->validate_data($product_id,"int");
        $product_info = array();
        if ($product_id != false)
        {
            $sql = "select product_name, product_description, ingredients, product_image from products where product_id =?";
            $res = $this->db->query($sql, array($product_id));
            if ($res)
            {
                $row = $res->row_array();

                $sql = "select category_id from product_and_category where product_id = ?";
                $res_category = $this->db->query($sql, array($product_id));

                //sql query to get cateogry name - use query bindings for better efficiency and safer
                $sql = "select category_name from product_categories where category_id = ?";
                foreach ($res_category->result_array() as $category_arr)
                {
                    $res_category_name = $this->db->query($sql, array($category_arr["category_id"]));
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
                $sql = "select special_sale_id from special_sales_and_product where product_id = ?";
                $res_special_sale = $this->db->query($sql, array($product_id));
                //Check if this product has a special sale id
                if ($res_special_sale->num_rows() > 0)
                {
                    $row_special_sale = $res_special_sale->row_array();
                    $sql = "select start_date, end_date, percentage_discount from special_sales where special_sale_id = ?";
                    $res_special_sale_info = $this->db->query($sql, array($row_special_sale["special_sale_id"]));
                    if ($res_special_sale_info->num_rows() > 0)
                    {
                        $row_special_sale_info = $res_special_sale_info->row_array();

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
        }
        return $product_info;
    }

    /*Function get all products of a product category*/
    public function get_products_in_category( $category_id )
    {
        /*Sanitize input*/
        $category_id = $this->main_page_model->validate_data($category_id,"int");
        $return_arr = array();
        if ($category_id != false)
        {
            $sql = "select product_id from product_and_category where category_id=?";
            $res = $this->db->query($sql, array($category_id));
            if ($res)
            {
                $sql_product = "select * from products where product_id=?";
                $sql_special_sale_id = "select special_sale_id from special_sales_and_product where product_id=?";
                $sql_special_sale = "select * from special_sales where special_sale_id=?";
                foreach ($res->result_array() as $row)
                {
                    $res_product = $this->db->query($sql_product, array($row["product_id"]));
                    if ($res_product->num_rows() > 0)
                    {
                        $row_product = $res_product->row_array();
                        //save product info first
                        $product_info = array(
                            'product_id' => $row_product["product_id"],
                            'product_name' => $row_product["product_name"],
                            'product_image' => $row_product["product_image"],
                            'product_price' => $row_product["product_price"]
                        );
                    }
                    //Getting special sale info
                    $product_info['isOnSale'] = 0; //default has to special sale
                    $res_special_sale_id = $this->db->query($sql_special_sale_id, array($row["product_id"]));
                    if ($res_special_sale_id->num_rows() > 0)
                    {
                        $row_special_sale_id = $res_special_sale_id->row_array();
                        $res_special_sale = $this->db->query($sql_special_sale, array($row_special_sale_id["special_sale_id"]));
                        if ($res_special_sale->num_rows() > 0)
                        {
                            $row_special_sale = $res_special_sale->row_array();
                            //check if to see if this product is still on sale
                            if ($this->main_page_model->check_my_date($row_special_sale["start_date"], "after") && $this->main_page_model->check_my_date($row_special_sale["end_date"], "before"))
                            {
                                $product_info['isOnSale'] = 1;
                            }
                            $product_info['percentage_discount'] = $row_special_sale["percentage_discount"];
                            if (isset($row_product))
                            {
                                $discounted = (1 - ($row_special_sale["percentage_discount"] / 100)) * $row_product["product_price"];
                                $discounted = number_format($discounted, 2, '.', ',');
                                $product_info['discounted'] = $discounted;
                            }
                        }
                    }
                    array_push($return_arr, $product_info);
                }
            }
        }
        return $return_arr;
    }
}