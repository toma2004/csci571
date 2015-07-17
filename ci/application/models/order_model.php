<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: NguyenTran
 * Date: 7/12/2015
 * Time: 12:09 AM
 */

class Order_model extends CI_Model {

    /*Constructor*/
    public function __construct()
    {
        parent::__construct();
        //load main_page_model to use functions in that class
        $this->load->model('main_page_model');
    }

    /*Function to place an order*/
    public function place_order ( $shopping_cart, $order_total_amount, $order_total_tax, $order_total_shipping, $cus_id )
    {
        //validate data
        $order_total_amount = $this->main_page_model->validate_data ($order_total_amount, "float");
        $order_total_tax = $this->main_page_model->validate_data ($order_total_tax, "float");
        $order_total_shipping = $this->main_page_model->validate_data ($order_total_shipping, "float");
        $cus_id = $this->main_page_model->validate_data ($cus_id, "int");
        if ($order_total_amount == false || $order_total_tax == false || $order_total_shipping == false || $cus_id == false)
        {
            return 'fail';
        }
        else
        {
            date_default_timezone_set('America/Los_Angeles');
            $cur_time = getdate();
            $mydate = implode('-', array($cur_time["year"], $cur_time["mon"], $cur_time["mday"]));
            $sql = "insert into orders (order_date,order_total_amount,order_total_tax,order_shipping_cost,customer_id) values (?,?,?,?,?)";
            $res = $this->db->query($sql, array($mydate,$order_total_amount,$order_total_tax,$order_total_shipping,$cus_id));
            if ($res)
            {
                //Need to retrieve order id
                $sql = "select order_id from orders order by order_id DESC limit 1";
                $res_order_id = $this->db->query($sql);
                if ($res_order_id->num_rows() > 0)
                {
                    $row_order_id = $res_order_id->row_array();
                    $product_count = 0;
                    $cart_count = count($shopping_cart);
                    /*Now insert each ordered item into order_items table in database*/
                    $sql = "insert into order_items values (?,?,?,?,?)";
                    foreach ($shopping_cart as $cart_items)
                    {
                        $res_order_item = $this->db->query($sql, array($row_order_id['order_id'],$cart_items['pid'], $cart_items['qty'], $cart_items['discounted'], $cart_items['special_sale_id']));
                        if ($res_order_item)
                        {
                            $product_count += 1;
                        }
                    }
                    /*Only clear shopping cart when we have added all info to database properly*/
                    if ($product_count == $cart_count)
                    {
                        //return true to the controller so that it knows placing order is successful and it need to clear the shopping cart
                        return 'success';
                    }
                    else
                    {
                        //roll back, revert the change in database and let customer know that placing order fails
                        $sql = "delete from order_items where order_id=?";
                        $this->db->query($sql, $row_order_id['order_id']);
                        $sql = "delete from orders where order_id=?";
                        $this->db->query($sql, $row_order_id['order_id']);
                        return 'fail';
                    }
                }
                else
                {
                    return 'fail';
                }
            }
            else
            {
                return 'fail';
            }
        }
    }

    /*Function to return all orders in database for a specific customer*/
    public function get_all_orders_info ( $cus_id )
    {
        $cus_id = $this->main_page_model->validate_data ($cus_id, "int");
        if ($cus_id == false)
        {
            return 'fail';
        }
        else
        {
            $sql = "select * from orders where customer_id=?";
            $res = $this->db->query($sql,$cus_id);
            if ($res->num_rows() > 0)
            {
                $return_array = array();
                foreach($res->result_array() as $order)
                {
                    array_push($return_array, array('order_date' => $order['order_date'],
                                                    'order_total_amount' => $order['order_total_amount'],
                                                    'order_total_tax' => $order['order_total_tax'],
                                                    'order_shipping_cost' => $order['order_shipping_cost'],
                                                    'order_id' => $order['order_id']));
                }
                if (count($return_array) == 0)
                {
                    return 'fail';
                }
                else
                {
                    return $return_array;
                }
            }
            else
            {
                //this customer has no past orders
                return 'no_order';
            }
        }
    }

    /*Function to get detail of an order*/
    public function get_order_detail( $order_id )
    {
        $order_id = $this->main_page_model->validate_data ($order_id, "int");
        if ($order_id == false)
        {
            return 'fail';
        }
        else
        {
            $sql = "select * from orders where order_id=?";
            $res_order_id = $this->db->query($sql,$order_id);
            if ($res_order_id->num_rows() > 0)
            {
                $row_order_id = $res_order_id->row_array();
                return array('order_date' => $row_order_id['order_date'],
                                      'order_total_amount' => $row_order_id['order_total_amount'],
                                      'order_total_tax' => $row_order_id['order_total_tax'],
                                      'order_shipping_cost' => $row_order_id['order_shipping_cost'],
                                      'order_id' => $row_order_id['order_id']);
            }
            else
            {
                return 'fail';
            }
        }
    }

    /*Function to get detail of order items*/
    public function get_order_items_detail( $order_id )
    {
        $order_id = $this->main_page_model->validate_data ($order_id, "int");
        if ($order_id == false)
        {
            return 'fail';
        }
        else
        {
            $sql = "select * from order_items where order_id=?";
            $res_order_item = $this->db->query($sql,$order_id);
            if ($res_order_item->num_rows() > 0)
            {
                $return_array = array();
                foreach ($res_order_item->result_array() as $row_order_item)
                {
                    array_push($return_array, array('product_id' => $row_order_item['product_id'],
                                                    'order_quantity' => $row_order_item['order_quantity'],
                                                    'p_price' => $row_order_item['p_price'],
                                                    'special_sale_id' => $row_order_item['special_sale_id']));
                }
                if (count($return_array) == 0)
                {
                    return 'fail';
                }
                else
                {
                    return $return_array;
                }
            }
            else
            {
                return 'fail';
            }
        }
    }
}