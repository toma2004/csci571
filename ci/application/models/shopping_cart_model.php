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
}