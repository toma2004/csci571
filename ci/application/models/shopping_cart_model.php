<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: NguyenTran
 * Date: 7/12/2015
 * Time: 12:09 AM
 */

class Shopping_cart_model extends CI_Model {

    /*Constructor*/
    public function __construct()
    {
        parent::__construct();
        //load main_page_model to use functions in that class
        $this->load->model('main_page_model');
    }

   /*Function to return data of a shopping cart stored in database*/
    public function get_cart_info( $cus_id )
    {
        $cus_id = $this->main_page_model->validate_data($cus_id, "int");
        $return_array = array();
        if ($cus_id == false)
        {
            return 'false';
        }
        else
        {
            $sql = "select * from shopping_cart where customer_id=?";
            $res = $this->db->query($sql, array($cus_id));
            if ($res ->num_rows() > 0)
            {
                foreach ($res->result_array() as $stored_cart_items)
                {
                    $item = array(
                        'product_id' => $stored_cart_items["product_id"],
                        'quantity' => $stored_cart_items["quantity"]
                    );
                    array_push($return_array,$item);
                }
                return $return_array;
            }
            else
            {
                return 'false'; //this customer has nothing in cart
            }
        }
    }

    /*Function to delete all items in cart in database*/
    public function delete_items_cart( $cus_id )
    {
        $cus_id = $this->main_page_model->validate_data($cus_id, "int");
        if ($cus_id == false)
        {
            return 'false';
        }
        else
        {
            $sql = "delete from shopping_cart where customer_id=?";
            $this->db->query($sql, array($cus_id));
            return 'true';
        }
    }

    /*Function to inset shopping cart to database*/
    public function insert_cart_to_database( $cus_id )
    {
        $cus_id = $this->main_page_model->validate_data($cus_id, "int");
        if ($cus_id == false)
        {
            return 'false';
        }
        else
        {
            $sql = "insert into shopping_cart values (?,?,?)";
            foreach ($_SESSION["shopping_cart"] as $cart_items)
            {
                $this->db->query($sql,array($cus_id,$cart_items["pid"],$cart_items["qty"]));
            }
            return 'true';
        }
    }

    /*Function to retrieve all info about the product being added to cart*/
    public function get_product_info_being_addedToCart( $product_id )
    {
        $product_id = $this->main_page_model->validate_data($product_id, "int");
        if ($product_id == false)
        {
            return 'false';
        }
        else
        {
            $sql = "select product_name, product_price, product_image from products where product_id=?";
            $res = $this->db->query($sql, $product_id);
            if ($res->num_rows() > 0)
            {
                $row_product = $res->row_array();
                $return_array = array(
                    'product_name' => $row_product["product_name"],
                    'product_price' => $row_product["product_price"],
                    'product_image' => $row_product["product_image"]
                );

                $isOnSale = 0; //default is not on sale
                $discounted = $row_product["product_price"];

                $sql = "select special_sale_id from special_sales_and_product where product_id=?";
                $res_special_sale_id = $this->db->query($sql,$product_id);
                if ($res_special_sale_id->num_rows() > 0)
                {
                    //this product is on sale. Check if this sale event is still valid based on today date
                    $row_special_sale_id = $res_special_sale_id->row_array();

                    $sql = "select * from special_sales where special_sale_id=?";
                    $res_special_sale = $this->db->query($sql, $row_special_sale_id["special_sale_id"]);
                    if ($res_special_sale->num_rows() > 0)
                    {
                        $row_special_sale = $res_special_sale->row_array();
                        if ($this->main_page_model->check_my_date($row_special_sale["start_date"], "after") && $this->main_page_model->check_my_date($row_special_sale["end_date"], "before"))
                        {
                            $isOnSale = 1;
                            $discounted = (1 - ($row_special_sale["percentage_discount"] / 100)) * $row_product["product_price"];
                            $discounted = number_format($discounted, 2, '.', ',');
                            $return_array['special_sale_id'] = $row_special_sale["special_sale_id"];
                        }
                    }
                }
                $return_array['discounted'] = $discounted;
                $return_array['isOnSale'] = $isOnSale;
                if (!$isOnSale)
                {
                    $return_array['special_sale_id'] = '0';
                }
                return $return_array;
            }
            else
            {
                return 'false';
            }
        }
    }
}