<?php
/**
 * User: Chris Tran
 * Date: 6/18/2015
 * Time: 3:41 PM
 */

if(!isset($_SESSION))
{
    session_start();
}

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
    if(!(isset($_SESSION['username'])) || !(isset($_SESSION['password'])) || !(isset($_SESSION['usertype'])))
    {
        require "prelogin.html";
        require 'postlogin.html';
    }
    #Check if user is an employee
    elseif (!in_array("employee", $check_types ))
    {
        require "prelogin.html";
        require 'postlogin.html';
    }
    #Add new product category
    elseif (isset($_POST["category_name"]))
    {
        add_product_category();
    }
    #Add new product
    elseif (isset($_POST["product_name"]))
    {
        add_product();
    }
    #For any thing else, back to Home employee page
    else
    {
        require "pre_employee_page.html";
        require "post_employee_page.html";
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

/*Function to validate price*/
function validate_data_price($price)
{
    $data = trim($price); //remove whitespaces
    $data = stripslashes($data); //remove all backslashes
    $data = htmlspecialchars($data);
    return filter_var($data,FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/^[0-9]*(?:\.[0-9]{0,2})?$/")));

}

/*Funciton to add new product category*/
function add_product_category()
{
    $category_name = validate_data($_POST["category_name"]);
    $category_description = validate_data($_POST["category_description"]);

    #Somehow one of the element is blank, fail with error
    if($category_name == '' || $category_description == '')
    {
        #Failed
        require "pre_employee_page.html";
        echo '<p style="color:red">ERROR: either category name or category description is missing. Please double check and do it again.'.'</p>';
        require "post_employee_page.html";
        return;
    }
    $conn = connectDB();

    $sql = "insert into product_categories (category_name,category_description) values ('".$category_name."','".$category_description."')";
    $res = mysql_query($sql);

    if (!$res)
    {
        #Failed to insert new category
        require "pre_employee_page.html";
        echo '<p style="color:red">ERROR: inserting new category.'.'</p>';
        require "post_employee_page.html";
    }
    else
    {
        #Woohoo! No error
        require "pre_employee_page.html";
        echo '<p style="color:blue">Successfully inserted new category.'.'</p>';
        require "post_employee_page.html";
    }
    disconnectDB($conn);
}

/*Function to add new product*/
function add_product()
{
    $product_name = validate_data($_POST["product_name"]);
    $price = validate_data_price($_POST["product_price"]);
    $product_category_id = filter_input(INPUT_POST,"product_category_add",FILTER_VALIDATE_INT);
    $product_description = validate_data($_POST["product_description"]);
    $ingredient = validate_data($_POST["ingredient"]);
    $recipe = validate_data($_POST["recipe"]);

    #double check to see if anything is unexpected
    if ($product_name == '' || $price == '' || $product_category_id == false || $product_category_id == NULL || $product_description == '' || $ingredient == '' || $recipe == '')
    {
        #Failed
        require "pre_employee_page.html";
        echo '<p style="color:red">ERROR: one of the fields is missing. Please double check and do it again.'.'</p>';
        require "post_employee_page.html";
        return;
    }
    $conn = connectDB();

    #check if the category id exists
    $sql = "select * from product_categories where category_id = '".$product_category_id."'";
    $res = mysql_query($sql);

    if (!($row = mysql_fetch_assoc($res)))
    {
        #No category id found. Return error since a product must belong to an existing category
        require "pre_employee_page.html";
        echo '<p style="color:red">ERROR: the category id '.$product_category_id.' you specified is not found. Therefore, the new product did not get added to the database. Please try again with a valid category id'.'</p>';
        require "post_employee_page.html";
        disconnectDB($conn);
        return;
    }

    #continue if everything is good
    $sql = "insert into products (product_name,product_price,product_description,ingredients,recipe) values ('".$product_name."','".$price."','".$product_description."','".$ingredient."','".$recipe."')";
    $res = mysql_query($sql);
    if (!$res)
    {
        #error in inserting new product
        require "pre_employee_page.html";
        echo '<p style="color:red">ERROR: inserting new product'.'</p>';
        require "post_employee_page.html";
    }
    else
    {
        $sql = "select product_id from products order by product_id DESC limit 1";
        $res = mysql_query($sql);
        if (!($row = mysql_fetch_assoc($res)))
        {
            require "pre_admin_page.html";
            echo '<p style="color:red">ERROR: inserting new product'.'</p>';
            require "post_admin_page.html";
            disconnectDB($conn);
            return;
        }
        #Save the product id
        $product_id = $row["product_id"];

        #Now put this product into a category in product_category table
        $sql = "insert into product_category values('".$product_id."','".$product_category_id."')";
        $res = mysql_query($sql);
        if (!$res)
        {
            #error inserting into product_category table
            require "pre_employee_page.html";
            echo '<p style="color:red">ERROR: inserting new product'.'</p>';
            require "post_employee_page.html";
        }
        else
        {
            #Woohoo! No error
            require "pre_employee_page.html";
            echo '<p style="color:blue">Successfully added new product to our database'.'</p>';
            require "post_employee_page.html";
        }
    }
    disconnectDB($conn);
}

?>