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
        //Prepare sql statement to get product info for all product in special sale and product table
        $sql = "select product_name,product_price,product_image from products where product_id=?";
        //prepare sql statement to get special sale detail
        $sql_special_sale = "select * from special_sales where special_sale_id=?";
        foreach ($res->result_array() as $row)
        {
            $res_product = $this->db->query($sql,$row["product_id"]);
            if ($res_product)
            {
                $row_product = $res_product->row_array(); //Get only 1 row which is what expected
                /*Getting special sale percentage discount*/
                $res_special_sale = $this->db->query($sql_special_sale, $row["special_sale_id"]);
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

    /*Function to validate data to avoid XSS attack*/
    function validate_data( $data, $type )
    {
        $data = trim($data); //remove whitespaces
        $data = stripslashes($data); //remove all backslashes
        $data = htmlspecialchars($data);
        if ($type == "first_name" || $type == "last_name" || $type == "city" || $type == "state" || $type == "country")
        {
            return filter_var($data,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/\D+/")));
        }
        else if ($type == "address")
        {
            return filter_var($data,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/([0-9])+\s+([A-Za-z])+.*/")));
        }
        else if ($type == "credit_card")
        {
            return filter_var($data,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/[0-9]{16}/")));
        }
        else if ($type == "security_code")
        {
            return filter_var($data,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/[0-9]{3}/")));
        }
        else if ($type == "exp_month")
        {
            return filter_var($data,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/[0-9]{1,2}/")));
        }
        else if ($type == "exp_year")
        {
            return filter_var($data,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/[0-9]{4}/")));
        }
        else if ($type == "phone")
        {
            return filter_var($data,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/[0-9]{10}/")));
        }
        else if ($type == "email")
        {
            return filter_var($data,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/[A-Za-z0-9]+@[A-Za-z0-9]+\.[a-z]{2,3}$/")));
        }
        else if ($type == "username")
        {
            return filter_var($data,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/[a-zA-Z0-9]+/")));
        }
        else if ($type == "int")
        {
            return filter_var($data,FILTER_VALIDATE_INT);
        }
        else if ($type == "float")
        {
            return filter_var($data,FILTER_VALIDATE_FLOAT);
        }
        return $data;
    }
}