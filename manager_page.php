<?php
/**
 * User: NguyenTran
 * Date: 6/21/2015
 * Time: 5:01 PM
 */

if(!isset($_SESSION))
{
    session_start();
}

if (!isset($_SESSION['last_activity']) || !isset($_SESSION['usertype']) || !isset($_SESSION['username']) || !isset($_SESSION['password']) || !isset($_SESSION['timeout']))
{
    require "prelogin.html";
    require "postlogin.html";
}
else
{
    $t = time();
    if (($t - $_SESSION['last_activity']) > 1800)
    {
        $_SESSION['timeout'] = 1;
        require "logout.php";
    }
    else
    {
        #session is not yet timeout. Reset time to give users another 30 mins
        $_SESSION['last_activity'] = time();

        #Get user type from SESSION array
        $check_types = explode(',', $_SESSION["usertype"]);

        #Check if user log in correctly
        if (!(isset($_SESSION['username'])) || !(isset($_SESSION['password'])) || !(isset($_SESSION['usertype'])))
        {
            require "prelogin.html";
            require 'postlogin.html';
        }
        #Check if user is a manager
        elseif (!in_array("manager", $check_types))
        {
            require "prelogin.html";
            require 'postlogin.html';
        }
        #Check if manager send request for employee search
        elseif (isset($_POST["employee_pay_range_low"]) || isset($_POST["employee_pay_range_high"]) || isset($_POST["manager_employee_user_type"]))
        {
            if($_POST["employee_pay_range_low"] != '' || $_POST["employee_pay_range_high"] != '' || $_POST["manager_employee_user_type"] != '')
            {
                display_result_employee_search();
            }
        }
        #Check if manager send request for product search
        elseif (isset($_POST["product_price_range_low"]) || isset($_POST["product_price_range_high"]) || isset($_POST["product_search_name"]) || isset($_POST["product_search_category"]))
        {
            if($_POST["product_price_range_low"] != '' || $_POST["product_price_range_high"] != '' || $_POST["product_search_name"] != '' || $_POST["product_search_category"] != '')
            {
                display_result_product_search();
            }
        }
        #For any thing else, back to Home employee page
        else
        {
            require "manager_page.html";
        }
    }
}

/*Function to connect to DB*/
function connectDB()
{
    $host = 'localhost';
    $user = 'root';
    $pass = 'ntcsci571hw2';

    $conn = mysql_connect($host, $user, $pass);
    if (!$conn)
    {
        die("Could not connect to database");
    }
    mysql_select_db('n2_internal_db',$conn);
    return $conn;
}

/*Function to disconnect from db*/
function disconnectDB($myconn)
{
    mysql_close($myconn);
}

/*Function to validate form data*/
function validate_data($data)
{
    $data = trim($data); //remove whitespaces
    $data = stripslashes($data); //remove all backslashes
    $data = htmlspecialchars($data);
    return $data;
}

/*Function to validate of input type number - price and percentage discount*/
function validate_data_number($price)
{
    $data = trim($price); //remove whitespaces
    $data = stripslashes($data); //remove all backslashes
    $data = htmlspecialchars($data);
    return filter_var($data,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/^[0-9]*(?:\.[0-9]{0,2})?$/")));
}

/*function to display result from employee search*/
function display_result_employee_search()
{
    $conn = connectDB();
    /*Select all employees who have these user types*/
    if ($_POST["manager_employee_user_type"] != '')
    {
        #Return all employees that have the selected type
        $arr_user_type = explode(',',$_POST["manager_employee_user_type"]);
        sort($arr_user_type);
        $userid_arr = array();
        $assoc_arr_userid_usertype = array();

        $sql = "select userid,usertype from users";
        $res = mysql_query($sql);
        $counter = 0;
        while($row = mysql_fetch_assoc($res))
        {
            $counter += 1;
            $arr_temp_user_type = explode(',',$row["usertype"]);
            if(check_subset($arr_temp_user_type,$arr_user_type))
            {
                array_push($userid_arr,$row["userid"]);
                $assoc_arr_userid_usertype[$row["userid"]] = $row["usertype"];
            }
        }
        if($counter == 0)
        {
            #error, no usertype/userid found in our database
            echo '<p style="color:red">Fatal error: there is no user in our database!</p><br/>';
        }
        else
        {
            if (count($userid_arr) == 0)
            {
                #None of employee has the requested user type
                echo '<p style="color:red">There is no employee who has the user type(s) '.$_POST["manager_employee_user_type"].' that you searched</p><br/>';
            }
            else
            {
                /*Select all employees who have these user types regardless of their pay range*/
                if($_POST["employee_pay_range_low"] == '' && $_POST["employee_pay_range_high"] == '')
                {
                    echo '<table id="table_employee_user_type">';
                    echo '<tr><th>Employee id</th><th>Employee first name</th><th>Employee last name</th><th>User type</th></tr>';
                    foreach ($userid_arr as $val)
                    {
                        $sql = "select employee_id, e_first_name, e_last_name from employees where userid='".$val."'";
                        $res = mysql_query($sql);
                        if (!$row=mysql_fetch_assoc($res))
                        {
                            #error this user id does not associate with any employee
                            echo '<p style="color:red">Fatal error: this user id '.$val.' does not belong to any employee</p><br/>';
                        }
                        else
                        {
                            echo '<tr><td>'.$row["employee_id"].'</td>';
                            echo '<td>'.$row["e_first_name"].'</td>';
                            echo '<td>'.$row["e_last_name"].'</td>';
                            echo '<td>'.$assoc_arr_userid_usertype[$val].'</td></tr>';
                        }
                    }
                    echo '</table>';
                }
                /*Select all employees who have these user types and whose salary is in pay range*/
                else
                {
                    $counter = 0;
                    echo '<table id="table_employee_user_type_pay_range">';
                    echo '<tr><th>Employee id</th><th>Employee first name</th><th>Employee last name</th><th>Employee salary</th><th>User type</th></tr>';
                    foreach ($userid_arr as $val)
                    {
                        $sql = "select employee_id, e_first_name, e_last_name, e_salary from employees where userid='" . $val . "' and e_salary >='" . $_POST["employee_pay_range_low"] . "' and e_salary <= '" . $_POST["employee_pay_range_high"] . "'";
                        $res = mysql_query($sql);
                        if ($row = mysql_fetch_assoc($res))
                        {
                            $counter += 1;
                            echo '<tr><td>' . $row["employee_id"] . '</td>';
                            echo '<td>' . $row["e_first_name"] . '</td>';
                            echo '<td>' . $row["e_last_name"] . '</td>';
                            echo '<td>' . $row["e_salary"] . '</td>';
                            echo '<td>' . $assoc_arr_userid_usertype[$val] . '</td></tr>';
                        }
                    }
                    echo '</table>';
                    if($counter == 0)
                    {
                        echo '<p style="color:red">There is no employee fitting your search criteria. Please try again</p><br/>';
                    }
                }
            }
        }
    }
    #Select all employees whose pay range is in range regardless of employee user type
    else if($_POST["manager_employee_user_type"] == '' && $_POST["employee_pay_range_low"] != '' && $_POST["employee_pay_range_high"] != '')
    {
        $sql = "select employee_id, e_first_name, e_last_name, e_salary from employees where e_salary >='".$_POST["employee_pay_range_low"]."' and e_salary <= '".$_POST["employee_pay_range_high"]."'";
        $res = mysql_query($sql);
        if (!$res)
        {
            echo '<p style="color:red">ERROR: cannot get employee salary info</p><br/>';
        }
        else
        {
            $counter = 0;
            echo '<table id="table_employee_pay_range">';
            echo '<tr><th>Employee id</th><th>Employee first name</th><th>Employee last name</th><th>Employee salary</th></tr>';
            while ($row = mysql_fetch_assoc($res))
            {
                $counter += 1;
                echo '<tr><td>'.$row["employee_id"].'</td>';
                echo '<td>'.$row["e_first_name"].'</td>';
                echo '<td>'.$row["e_last_name"].'</td>';
                echo '<td>'.$row["e_salary"].'</td></tr>';
            }
            echo '</table>';
            if($counter == 0)
            {
                echo '<p style="color:red">There is no employee whose salary is in pay range from '.$_POST["employee_pay_range_low"].' to '.$_POST["employee_pay_range_high"].'</p><br/>';
            }
        }

    }
    disconnectDB($conn);
}

/*Function to check if an array is a subset of another larger array*/
function check_subset($arr,$sorted_larger_arr)
{
    $total_element = count($sorted_larger_arr);
    foreach ($arr as $arr_val)
    {
        $temp = binarySearch($sorted_larger_arr,0,$total_element-1,$arr_val);
        if ($temp == false)
        {
            return false;
        }
    }
    return true;
}
/*binary search*/
function binarySearch($arr,$first,$last,$val)
{
    if ($first > $last)
    {
        return false;
    }
    $mid = floor(($first+$last)/2);
    if ($arr[(int)$mid] == $val)
    {
        return true;
    }
    elseif ($arr[(int)$mid] > $val)
    {
        return binarySearch($arr,$first,$mid-1,$val);
    }
    else
    {
        return binarySearch($arr,$mid+1,$last,$val);
    }
}

/*Function to display product search result*/
function display_result_product_search()
{
    $conn = connectDB();
    $err_msg = "";
    /*Select all product name with name like requested name regardless of product category and price range
    * or select product name based on price range
    */
    if ($_POST["product_search_category"] == '' && ($_POST["product_search_name"] != '' || ($_POST["product_price_range_low"] != '' && $_POST["product_price_range_high"] != '')))
    {
        if ($_POST["product_price_range_low"] == '' && $_POST["product_price_range_high"] == '')
        {
            $sql = "select product_id, product_name, product_price from products where product_name like '%".$_POST["product_search_name"]."%'";
        }
        elseif ($_POST["product_search_name"] == '')
        {
            $sql = "select product_id, product_name, product_price from products where product_price >= '".$_POST["product_price_range_low"]."' and product_price <= '".$_POST["product_price_range_high"]."'";
        }
        else
        {
            $sql = "select product_id, product_name, product_price from products where product_name like '%".$_POST["product_search_name"]."%' and product_price >= '".$_POST["product_price_range_low"]."' and product_price <= '".$_POST["product_price_range_high"]."'";
        }
        $res = mysql_query($sql);
        if (!$res)
        {
            #sql query error
            echo '<p style="color:red">ERROR: retrieving product info</p><br/>';
        }
        else
        {
            $counter = 0;
            echo '<table id="table_product_search_name">';
            echo '<tr><th>Product id</th><th>Product name</th><th>Product price</th></tr>';
            while($row = mysql_fetch_assoc($res))
            {
                $counter += 1;
                echo '<tr><td>'.$row["product_id"].'</td>';
                echo '<td>'.$row["product_name"].'</td>';
                echo '<td>'.$row["product_price"].'</td></tr>';
            }
            echo '</table>';
            if($counter == 0)
            {
                echo '<p style="color:red">There is no product matching your specified product name string</p><br/>';
            }
        }
    }
    /*Select all product category with name like requested name regardless of product name and price range*/
    else if($_POST["product_search_category"] != '')
    {
        if ($_POST["product_price_range_low"] == '' && $_POST["product_price_range_high"] == '' && $_POST["product_search_name"] == '')
        {
            $sql = "select category_id, category_name, category_description from product_categories where category_name like '%".$_POST["product_search_category"]."%'";
            $res = mysql_query($sql);
            if (!$res)
            {
                #sql query error
                echo '<p style="color:red">ERROR: retrieving category info</p><br/>';
            }
            else
            {
                $counter = 0;
                echo '<table id="table_product_category_search_name">';
                echo '<tr><th>Category id</th><th>Category name</th><th>Category description</th></tr>';
                while($row = mysql_fetch_assoc($res))
                {
                    $counter += 1;
                    echo '<tr><td>'.$row["category_id"].'</td>';
                    echo '<td>'.$row["category_name"].'</td>';
                    echo '<td>'.$row["category_description"].'</td></tr>';
                }
                echo '</table>';
                if($counter == 0)
                {
                    echo '<p style="color:red">There is no product category matching your specified category name string</p><br/>';
                }
            }
        }
        else
        {
            $sql = "select category_id, category_name, category_description from product_categories where category_name like '%".$_POST["product_search_category"]."%'";
            $res = mysql_query($sql);
            if (!$res)
            {
                #sql query error
                echo '<p style="color:red">ERROR: retrieving category info</p><br/>';
            }
            else
            {
                $counter = 0;
                echo '<table id="table_product_category_search_name_price">';
                echo '<tr><th>Product id</th><th>Product name</th><th>Product price</th><th>Category_id</th><th>Category_name</th></tr>';
                while($row = mysql_fetch_assoc($res))
                {
                    $counter += 1;

                    $sql = "select product_id from product_and_category where category_id='".$row["category_id"]."'";
                    $res_product = mysql_query($sql);
                    $counter_product = 0;
                    while ($row_product = mysql_fetch_assoc($res_product))
                    {
                        $counter_product += 1;
                        if ($_POST["product_search_name"] != '' && $_POST["product_price_range_low"] == '' && $_POST["product_price_range_high"] == '')
                        {
                            $sql = "select product_id, product_name, product_price from products where product_id='".$row_product["product_id"]."' and product_name like '%".$_POST["product_search_name"]."%'";
                        }
                        elseif ($_POST["product_search_name"] == '' && $_POST["product_price_range_low"] != '' && $_POST["product_price_range_high"] != '')
                        {
                            $sql = "select product_id, product_name, product_price from products where product_id='".$row_product["product_id"]."' and product_price >= '".$_POST["product_price_range_low"]."' and product_price <= '".$_POST["product_price_range_high"]."'";
                        }
                        else
                        {
                            $sql = "select product_id, product_name, product_price from products where product_id='".$row_product["product_id"]."' and product_name like '%".$_POST["product_search_name"]."%' and product_price >= '".$_POST["product_price_range_low"]."' and product_price <= '".$_POST["product_price_range_high"]."'";
                        }
                        $res_final = mysql_query($sql);
                        $counter_final = 0;
                        while ($row_final = mysql_fetch_assoc($res_final))
                        {
                            $counter_final += 1;
                            echo '<tr><td>'.$row_final["product_id"].'</td>';
                            echo '<td>'.$row_final["product_name"].'</td>';
                            echo '<td>'.$row_final["product_price"].'</td>';
                            echo '<td>'.$row["category_id"].'</td>';
                            echo '<td>'.$row["category_name"].'</td></tr>';
                        }
                        if($counter_final == 0)
                        {
                            #error

                        }
                    }
                    if($counter_product == 0)
                    {
                        #error
                    }

                }
                echo '</table>';
                if($counter == 0)
                {
                    echo '<p style="color:red">There is no product category matching your specified category name string</p><br/>';
                }
            }
        }
    }
    disconnectDB($conn);
}

?>