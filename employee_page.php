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
    #Add special sales
    elseif (isset($_POST["product_specialsales"]))
    {
        add_special_sale();
    }
    #display product being chosen for modified
    elseif (isset($_POST["product_modified"]))
    {
        display_product_info_to_modify();
    }
    #Modify product based on employee's changes
    elseif (isset($_POST["mysubmit_modified2_product"]))
    {
        if($_POST["modified_product_name"] != '' || $_POST["modified_product_price"] != '' || $_POST["modified_product_description"] != '' || $_POST["modified_ingredients"] != '' || $_POST["modified_recipe"] != '' || isset($_POST["employee_modified2_product_cb1"]))
        {
            modify_product_info();
        }
        else
        {
            require "pre_employee_page.html";
            echo '<p style="color:blue">No product info has been changed since you did not select anything'.'</p>';
            require "post_employee_page.html";
        }
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
    if ($product_name == '' || $price == false || $product_category_id == false || $product_category_id == NULL || $product_description == '' || $ingredient == '' || $recipe == '')
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
        $sql = "insert into product_and_category values('".$product_id."','".$product_category_id."')";
        $res = mysql_query($sql);
        if (!$res)
        {
            #error inserting into product_category table

            #Since this operation fail, we need to roll back and remove the product from product table
            $sql = "delete from products where product_id='".$product_id."'";
            $res = mysql_query($sql);
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

/*Function to add special sale*/
function add_special_sale()
{
    $start_date = validate_data($_POST["mystart_date"]);
    $end_date = validate_data($_POST["myend_date"]);

    $product_id = filter_input(INPUT_POST,"product_specialsales",FILTER_VALIDATE_INT);
    $percentage_discount = validate_data_price($_POST["percentage_discount"]);

    $error_msg = '';
    $hasError = false;
    /*Check date on server side*/
    $date1_arr = explode('-',$start_date);
    if (!checkdate($date1_arr[1],$date1_arr[2],$date1_arr[0]))
    {
        #Wrong date
        $hasError = true;
        $error_msg .= "ERROR: start date format is not correct\r\n";
    }

    $date2_arr = explode('-',$end_date);
    if (!checkdate($date2_arr[1],$date2_arr[2],$date2_arr[0]))
    {
        #Wrong date
        $hasError = true;
        $error_msg .= "ERROR: end date format is not correct\r\n";
    }

    if ($product_id == NULL || $product_id == false)
    {
        $hasError = true;
        $error_msg .= "ERROR: product  id is either not an integer and left blank\r\n";
    }

    if ($percentage_discount == false)
    {
        $hasError = true;
        $error_msg .= "ERROR: percentage discount is not in correct format\r\n";
    }

    #check if we have error
    if ($hasError)
    {
        require "pre_employee_page.html";
        echo '<p style="color:red">'.$error_msg.'</p>';
        require "post_employee_page.html";
        return;
    }

    #if no error, continue
    $conn = connectDB();
    $sql = "select * from products where product_id='".$product_id."'";
    $res = mysql_query($sql);
    if(!($row = mysql_fetch_assoc($res)))
    {
        #product it not found
        require "pre_employee_page.html";
        echo '<p style="color:red">ERROR: product id '.$product_id.' is not found. Therefore, could not add special sale for this product'.'</p>';
        require "post_employee_page.html";
        disconnectDB($conn);
        return;
    }
    $sql = "insert into special_sales (product_id,start_date,end_date,percentage_discount) values ('".$product_id."','".$start_date."','".$end_date."','".$percentage_discount."')";
    $res = mysql_query($sql);
    if (!$res)
    {
        #Failed to insert
        require "pre_employee_page.html";
        echo '<p style="color:red">ERROR: failed to insert data to special sale database'.'</p>';
        require "post_employee_page.html";
    }
    else
    {
        #woohoo! no error
        require "pre_employee_page.html";
        echo '<p style="color:blue">Successfully added special sale event for product id '.$product_id.'</p>';
        require "post_employee_page.html";
    }
    disconnectDB($conn);
}

/*Function to display info of a product that is selected for modification*/
function display_product_info_to_modify()
{
    #Validate the input once again
    $product_id = validate_data($_POST['product_modified']);
    $validate_product_id = filter_input(INPUT_POST,"product_modified",FILTER_VALIDATE_INT);
    if ($product_id == '' || $validate_product_id == NULL || $validate_product_id == false)
    {
        #Input error
        require "pre_employee_page.html";
        echo '<p style="color:red">ERROR: product id is not valid'.'</p>';
        require "post_employee_page.html";
    }
    else
    {
        $conn = connectDB();
        #Check if we have this product id in our database
        $sql = "select * from products where product_id = '".$product_id."'";
        $res = mysql_query($sql);
        if (!($row = mysql_fetch_assoc($res)))
        {
            require "pre_employee_page.html";
            echo '<p style="color:red">ERROR: Could not find product id '.$product_id.' in our database.'.'</p>';
            require "post_employee_page.html";
            disconnectDB($conn);
            return;
        }
        #continue if we have product id
        ?>

             <!-- End php and display html -->
        <!DOCTYPE html>
        <html>
        <head lang="en">
            <meta charset="UTF-8"/>
            <meta name="author" content="Nguyen Tran"/>

           <link rel="stylesheet" type="text/css" href="employee_page_style.css"/> <!-- link to external css file
            <!--<link rel="shortcut icon" type="image/jpg" href="flower_crown1.jpg"></link> -->
            <script src="employee_page_js.js"></script>
            <title>Product info</title>
        </head>
        <body>
        <div id="employee_page_modify2_product">
            <h1><?php echo 'Product '.$row["product_name"].' info (product id '.$product_id.')';
        ?></h1>

            <p id="employee_page_modify2_product_errmsg" style="color:red"></p>

            <span style="font-weight: bold;position:absolute; left: 13%">Current Value</span>
            <span style="font-weight: bold; position:absolute; left: 50%">Change to value</span><br/><br/>

            <form id="modified_product_info" action="employee_page.php" method="POST">
                <!--hidden input to send server the product id that needs to be modified -->
                <input type="hidden" name="hidden_product_id" value="<?php echo $product_id; ?>"/>

            <?php
            #Product name & Price
            echo 'Product name: ';
            echo '<span style="position:absolute; left: 13%">'.$row["product_name"].'</span>';
            echo '<input type="text" id="modified_product_name" name="modified_product_name" pattern="([a-zA-Z0-9]+)\s*([a-zA-Z0-9])*" maxlength="255" style="position:absolute; left: 50%"/><br/><br/>';

            echo 'Product price: ';
            echo '<span style="position:absolute; left: 13%">'.$row["product_price"].'</span>';
            echo '<input type="number" id="modified_product_price" name="modified_product_price" pattern="(^\d*(?:\.\d{0,2})?$)" step="0.01" min="0" max="9999" style="position:absolute; left: 50%"/><br/><br/>';

            #Product description
            echo '<p class=formfield>';
            echo 'Product description: ';
            echo '<textarea rows="30" cols="50" readonly style="position:relative; left: 5%">'.$row["product_description"].'</textarea>';
            echo '<textarea id="modified_product_description" name="modified_product_description" rows="30" cols="50" style="position:absolute; left: 50%"></textarea></p><br/>';

            #Ingredients
            echo '<p class=formfield>';
            echo 'Ingredient: ';
            echo '<textarea rows="30" cols="50" readonly style="position:relative; left: 8%">'.$row["ingredients"].'</textarea>';
            echo '<textarea id="modified_ingredients" name="modified_ingredients" rows="30" cols="50" style="position:absolute; left: 50%"></textarea></p><br/>';

            #Recipe
            echo '<p class=formfield>';
            echo 'Recipe: ';
            echo '<textarea rows="30" cols="50" readonly style="position:relative; left: 9%">'.$row["recipe"].'</textarea>';
            echo '<textarea id="modified_recipe" name="modified_recipe" rows="30" cols="50" style="position:absolute; left: 50%"></textarea></p><br/>';

            #Retrieve product category
            $sql = "select * from product_and_category where product_id ='".$row["product_id"]."'";
            $res = mysql_query($sql);

            $category_id_arr = array();
            $counter = 0; //This is used to check if mysql_fetch_assoc return false
            #Store returned category id into an array in case this product belongs to more than 1 category
            while ($row = mysql_fetch_assoc($res))
            {
                $counter += 1;
                array_push($category_id_arr,$row["category_id"]);
            }
            if ($counter == 0)
            {
                require "pre_employee_page.html";
                echo '<p style="color:red">ERROR: Cannot retrieve category for product id '.$product_id.'.This is a critical error in database. Please consult your database admin to resolve'.'</p>';
                require "post_employee_page.html";
                disconnectDB($conn);
                return;
            }
            $category_str = implode(',',$category_id_arr);

            #Category that this product belongs to
            echo '<p class=formfield>';
            echo 'Product belongs to the'.'<br/>'.'following category id(s): ';
            echo '<textarea rows="5" cols="20" readonly style="position:relative; left: 3%">'.$category_str.'</textarea>';

            #Print out checkboxes of all available category in database
            $sql = "select * from product_categories";
            $res = mysql_query($sql);

            $checkbox_id = "employee_modified2_product_cb";
            $myindex = 1;
            $initial_px = 50;
            $second_initial_px = 51;
            while($row = mysql_fetch_assoc($res))
            {
               $cb_id = $checkbox_id.$myindex;
                echo '<input type="checkbox" id='.$cb_id.' name="employee_modified2_product_cb1[]" value='.$row["category_id"].' style="position:absolute; left:'.$initial_px.'%"/><span style="position:absolute; left:'.$second_initial_px.'%">'.$row["category_id"].'</span>';
                $initial_px += 5;
                $second_initial_px += 5;
                $myindex += 1;
            }
            echo '</p><br/><br/>';
            if ($myindex == 1)
            {
                require "pre_employee_page.html";
                echo '<p style="color:red">ERROR: Cannot retrieve any category. This is a critical error in database. Please consult your database admin to resolve'.'</p>';
                require "post_employee_page.html";
                disconnectDB($conn);
                return;
            }


            echo '<button type="submit" value="go_homepage_employee">Home</button>';
            echo '<button type="submit" onclick="return validate_modify2_product()" name="mysubmit_modified2_product" value="mysubmit_modified2_product" style="position:relative; left:15px;">Submit</button>';

        ?>
            </form>
        </div>
        </body>
        </html>

        <?php
            disconnectDB($conn);
    }
}

/*Function to modify product info*/
function modify_product_info()
{
    #Validate the input once again
    $product_id = validate_data($_POST['hidden_product_id']);
    $validate_product_id = filter_input(INPUT_POST,"hidden_product_id",FILTER_VALIDATE_INT);
    if ($product_id == '' || $validate_product_id == NULL || $validate_product_id == false)
    {
        #Input error
        require "pre_employee_page.html";
        echo '<p style="color:red">ERROR: product id is not valid'.'</p>';
        require "post_employee_page.html";
    }
    else
    {
        /*Connect to our db*/
        $conn = connectDB();
        $errmsg = "";

        if($_POST['modified_product_name'] != '')
        {
            $product_name = validate_data($_POST["modified_product_name"]);
            $sql = "update products set product_name='".$product_name."' where product_id='".$product_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                #Failed to update
                $errmsg .= "Failed to update product name.\r\n";
            }
        }
        if($_POST['modified_product_price'] != '')
        {
            $product_price = validate_data_price($_POST["modified_product_price"]);
            if($product_price == false)
            {
                #invalid product price
                $errmsg .= "Invalid product price.\r\n";
            }
            else
            {
                $sql = "update products set product_price='" . $product_price . "' where product_id='" . $product_id . "'";
                $res = mysql_query($sql);
                if (!$res) {
                    #Failed to update
                    $errmsg .= "Failed to update product price.\r\n";
                }
            }
        }
        if($_POST['modified_product_description'] != '')
        {
            $product_description = validate_data($_POST["modified_product_description"]);
            $sql = "update products set product_description='".$product_description."' where product_id='".$product_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                #Failed to update
                $errmsg .= "Failed to update product description.\r\n";
            }
        }
        if($_POST['modified_ingredients'] != '')
        {
            $ingredient = validate_data($_POST["modified_ingredients"]);
            $sql = "update products set ingredients='".$ingredient."' where product_id='".$product_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                #Failed to update
                $errmsg .= "Failed to update ingredients.\r\n";
            }
        }
        if($_POST['modified_recipe'] != '')
        {
            $recipe = validate_data($_POST["modified_recipe"]);
            $sql = "update products set recipe='".$recipe."' where product_id='".$product_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                #Failed to update
                $errmsg .= "Failed to update recipe.\r\n";
            }
        }
        /*Change category*/
        if(isset($_POST['employee_modified2_product_cb1']))
        {
            #Check to see if the category employee wants to change it actually exists in our database
            $sql = "select category_id from product_categories";
            $res = mysql_query($sql);
            $counter = 0;
            $category_id_arr = array();
            while ($row = mysql_fetch_assoc($res))
            {
                $counter += 1;
                array_push($category_id_arr,$row["category_id"]);
            }
            if($counter == 0)
            {
                require "pre_employee_page.html";
                echo '<p style="color:red">ERROR: modifying product info'.'</p>';
                require "post_employee_page.html";
                disconnectDB($conn);
                return;
            }
            #check if the input of category is actually in our database
            sort($category_id_arr);
            if(!check_subset($_POST['employee_modified2_product_cb1'],$category_id_arr))
            {
                require "pre_employee_page.html";
                echo '<p style="color:red">ERROR: modifying product info1'.'</p>';
                require "post_employee_page.html";
                disconnectDB($conn);
                return;
            }
            #Delete the old record
            $sql = "delete from product_and_category where product_id ='".$product_id."'";
            $res = mysql_query($sql);
            if(!$res)
            {
                require "pre_employee_page.html";
                echo '<p style="color:red">ERROR: modifying product info2'.'</p>';
                require "post_employee_page.html";
                disconnectDB($conn);
                return;
            }

            #Everything good, continue
            foreach ($_POST['employee_modified2_product_cb1'] as $val)
            {
                $sql = "insert into product_and_category values ('".$product_id."','".$val."')";
                $res = mysql_query($sql);
                if(!$res)
                {
                    require "pre_employee_page.html";
                    echo '<p style="color:red">ERROR: modifying product info3'.'</p>';
                    require "post_employee_page.html";
                    disconnectDB($conn);
                    return;
                }
            }
        }
        disconnectDB($conn);
        #Check for error message
        if ($errmsg == '')
        {
            #No Error, woohoo!
            require "pre_admin_page.html";
            echo '<p style="color:blue">Successfully modified info for product id '.$product_id.'</p>';
            require "post_admin_page.html";
        }
        else
        {
            require "pre_admin_page.html";
            echo '<p style="color:red">'.$errmsg.'</p>';
            require "post_admin_page.html";
        }
    }
}

/*Function to check if an array is a subset of another larger array*/
function check_subset($arr,$sorted_larger_arr)
{
    $total_element = count($sorted_larger_arr);
    foreach ($arr as $arr_val)
    {
        if(!binarySearch($sorted_larger_arr,0,$total_element-1,$arr_val))
        {
            return false;
            break;
        }
    }
}
/*binary search*/
function binarySearch($arr,$first,$last,$val)
{
    if ($first >= $last)
    {
        return 0;
    }
    $mid = floor(($first+$last)/2);
    echo $arr[$mid];
    if ($arr[$mid] == $val)
    {
        return 1;
    }
    elseif ($arr[$mid] > $val)
    {
        return binarySearch($arr,$first,$mid-1,$val);
    }
    else
    {
        return binarySearch($arr,$mid+1,$last,$val);
    }
}
?>