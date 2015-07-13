<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: NguyenTran
 * Date: 7/12/2015
 * Time: 12:09 AM
 */

class Main_page_model extends CI_Model {

    /*Function to get special sale display on main webpage*/
    public function get_special_sale_display()
    {
        $sql = "select special_sale_id, product_id from special_sales_and_product";
        $res = $this->db->query($sql);
        $return_arr = array();
        foreach ($res->result_array() as $row)
        {
            $sql = "select product_name,product_price,product_image from products where product_id='".$row["product_id"]."'";
            $res_product = $this->db->query($sql);
            if ($res_product)
            {
                $row_product = $res_product->row_array(); //Get only 1 row which is what expected
                /*Getting special sale percentage discount*/
                $sql = "select * from special_sales where special_sale_id='".$row["special_sale_id"]."'";
                $res_special_sale = $this->db->query($sql);
                if ($res_special_sale)
                {
                    $row_special_sale = $res_special_sale->row_array();
                    /*Only return those product that is still on sale*/
                    if ($this->check_my_date($row_special_sale["start_date"], "after") && $this->check_my_date($row_special_sale["end_date"], "before"))
                    {
                        /*Calculate discount price*/
                        $discounted = (1 - ($row_special_sale["percentage_discount"] / 100)) * $row_product["product_price"];
                        $discounted = number_format($discounted, 2, '.', ',');
                        $special_sale_item = array(
                            'product_image' => $row_product["product_image"],
                            'product_name' => $row_product["product_name"],
                            'product_id' => $row["product_id"],
                            'percentage_discount' => $row_special_sale["percentage_discount"],
                            'product_price' => $row_product["product_price"],
                            'discounted' => $discounted
                        );
                        /*Append this info to our final return array*/
                        array_push($return_arr,$special_sale_item);
                    }
                }
            }
        }
        return $return_arr;
    }

    /*Function to return all category for drop-down menu*/
    public function get_category_dropDown_list()
    {
        $sql = "select category_id, category_name from product_categories";
        $res = $this->db->query($sql);
        $return_array = array();
        foreach ($res->result_array() as $row)
        {
            $category_item = array(
                'category_name' => $row["category_name"],
                'category_id' => $row["category_id"]
            );
            array_push($return_array,$category_item);
        }
        return $return_array;
    }

    /*Function to check if a given date is before/after the current date*/
    public function check_my_date ( $date_str, $intr)
    {
        date_default_timezone_set('America/Los_Angeles');
        $cur_time = getdate();
        $date_check = explode('-',$date_str);
        #year - $date_check[0]
        #month - $date_check[1]
        #day - $date_check[2]
        if (count($date_check) != 3)
        {
            #Date is not in correct format
            return false;
        }
        else
        {
            foreach ($date_check as $val)
            {
                #Convert to int
                $val = intval($val);
            }
        }
        $y = $cur_time["year"] - $date_check[0];
        $m = $cur_time["mon"] - $date_check[1];
        $d = $cur_time["mday"] - $date_check[2];

        if ($intr == "after") /*Check to see if this date str is after today date*/
        {
            if($y < 0)
            {
                return false;
            }
            else if ($y == 0)
            {
                if ($m < 0)
                {
                    return false;
                }
                else if ($m == 0)
                {
                    if ($d < 0)
                    {
                        return false;
                    }
                }
            }
        }
        else if ($intr == "before") /*Check to see if this date str is before today date*/
        {
            if($y > 0)
            {
                return false;
            }
            else if ($y == 0)
            {
                if ($m > 0)
                {
                    return false;
                }
                else if ($m == 0)
                {
                    if ($d > 0)
                    {
                        return false;
                    }
                }
            }
        }
        return true;
    }
}