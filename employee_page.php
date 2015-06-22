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
    } else
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
        #Check if user is an employee
        elseif (!in_array("employee", $check_types))
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
            if ($_POST["modified_product_name"] != '' || $_POST["modified_product_price"] != '' || $_POST["modified_product_description"] != '' || $_POST["modified_ingredients"] != '' || $_POST["modified_recipe"] != '' || isset($_POST["employee_modified2_product_cb1"]) || isset($_POST["employee_modified2_product_radio1"]))
            {
                modify_product_info();
            }
            else
            {
                require "pre_employee_page.html";
                echo '<p style="color:blue">No product info has been changed since you did not select anything' . '</p>';
                require "post_employee_page.html";
            }
        }
        #display category being chosen for modified
        elseif (isset($_POST["category_modified"]))
        {
            display_category_info_to_modify();
        }
        #modify category based on employee's changes
        elseif (isset($_POST["mysubmit_modify3_category"]))
        {
            if ($_POST["modified_category_name"] != '' || $_POST["modified_category_description"] != '')
            {
                modify_category_info();
            }
            else
            {
                require "pre_employee_page.html";
                echo '<p style="color:blue">No category info has been changed since you did not select anything' . '</p>';
                require "post_employee_page.html";
            }
        }
        #display special sale being chosen for modified
        elseif (isset($_POST["specialsale_modified"]))
        {
            display_special_sale_info_to_modify();
        }
        #Modify special sale based on employee's changes
        elseif (isset($_POST["mysubmit_modified4_specialsale"]))
        {
            if ($_POST["modified_specialsale_start_date"] != '' || $_POST["modified_specialsale_end_date"] != '' || $_POST["modified_specialsale_percentage_discount"] != '' || isset($_POST["employee_modified4_special_sale_cb1"]))
            {
                modify_special_sale_info();
            }
            else
            {
                require "pre_employee_page.html";
                echo '<p style="color:blue">No special sale info has been changed since you did not select anything' . '</p>';
                require "post_employee_page.html";
            }
        }
        #Delete product
        elseif (isset($_POST["delete_product"]))
        {
            delete_product();
        }
        #Delete category
        elseif (isset($_POST["delete_category"]))
        {
            delete_category();
        }
        #Determine if employee wants to remove all products before removing the category
        #This is a second part of delete category
        elseif (isset($_POST["delete_products_from_category_yes"]))
        {
            delete_all_product_delete_category();
        }
        #delete special sale
        elseif (isset($_POST["delete_specialsale"]))
        {
            delete_special_sale();
        }
        #For any thing else, back to Home employee page
        else
        {
            require "pre_employee_page.html";
            require "post_employee_page.html";
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
    $sql = "insert into special_sales (start_date,end_date,percentage_discount) values ('".$start_date."','".$end_date."','".$percentage_discount."')";
    $res = mysql_query($sql);
    if (!$res)
    {
        #Failed to insert
        require "pre_employee_page.html";
        echo '<p style="color:red">ERROR: failed to insert data to special sale database'.'</p>';
        require "post_employee_page.html";
        disconnectDB($conn);
        return;
    }
    #Everything good, continue
    #Get the newest special sale id
    $sql = "select special_sale_id from special_sales order by special_sale_id DESC limit 1";
    $res = mysql_query($sql);
    if (!($row = mysql_fetch_assoc($res)))
    {
        #Failed to insert
        require "pre_employee_page.html";
        echo '<p style="color:red">ERROR: failed to insert data to special sale database'.'</p>';
        require "post_employee_page.html";
        disconnectDB($conn);
        return;
    }
    $special_sale_id = $row["special_sale_id"];
    #Try to associate this special sale id with product id. Could fail if product id is already associated with another special sale
    $sql = "insert into special_sales_and_product values ('".$special_sale_id."','".$product_id."')";
    $res = mysql_query($sql);
    if(!$res)
    {
        #Error inserting or product id is already associated with another special sale
        require "pre_employee_page.html";
        echo '<p style="color:red">Special sale is added to our database, BUT the product id '.$product_id.' can NOT be associated with this special sale since it is already associated with another special sale event. You can always modify the association of product and special sale event by going to Modify option'.'</p>';
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

            #Retrieve special sale event
            $sql = "select * from special_sales_and_product where product_id ='".$product_id."'";
            $res = mysql_query($sql);

            $counter = 0; //This is used to check if mysql_fetch_assoc return false
            $special_sale_id = '';
            #Store returned category id into an array in case this product belongs to more than 1 category
            while ($row = mysql_fetch_assoc($res))
            {
                $counter += 1;
                $special_sale_id = $row["special_sale_id"];
            }
            if ($counter == 0)
            {
                $special_sale_id = 'No special sale id is associated with this product';
            }

            #Special sale that this product belongs to
            echo '<p class=formfield>';
            echo 'Product belongs to the'.'<br/>'.'following special sale id: ';
            echo '<textarea rows="5" cols="20" readonly style="position:relative; left: 3%">'.$special_sale_id.'</textarea>';

            #Print out radio buttons of all available special sale in database
            $sql = "select * from special_sales";
            $res = mysql_query($sql);

            $radio_id = "employee_modified2_product_radio_id";
            $myindex = 1;
            $initial_px = 50;
            $second_initial_px = 51;
            while($row = mysql_fetch_assoc($res))
            {
                $rd_id = $radio_id.$myindex;
                echo '<input type="radio" id='.$rd_id.' name="employee_modified2_product_radio1" value='.$row["special_sale_id"].' style="position:absolute; left:'.$initial_px.'%"/><span style="position:absolute; left:'.$second_initial_px.'%">'.$row["special_sale_id"].'</span>';
                $initial_px += 5;
                $second_initial_px += 5;
                $myindex += 1;
            }
            if ($myindex == 1)
            {
                echo '<span style="position:absolute; left: 50%">There is no available special sale in our database</span>';
            }
            echo '</p><br/><br/>';

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
                echo '<p style="color:red">ERROR: modifying product info'.'</p>';
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
                echo '<p style="color:red">ERROR: modifying product info'.'</p>';
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
                    echo '<p style="color:red">ERROR: modifying product info'.'</p>';
                    require "post_employee_page.html";
                    disconnectDB($conn);
                    return;
                }
            }
        }
        /*Change special sale event*/
        if (isset($_POST["employee_modified2_product_radio1"]))
        {
            #Check to see if the special sale employee wants to change it actually exists in our database
            $sql = "select special_sale_id from special_sales";
            $res = mysql_query($sql);
            $counter = 0;
            $isExisted = false;
            while ($row = mysql_fetch_assoc($res))
            {
                if($_POST["employee_modified2_product_radio1"] == $row["special_sale_id"])
                {
                    $isExisted = true;
                }
                $counter += 1;
            }
            if ($counter == 0 || $isExisted == false)
            {
                $errmsg .= "Failed to modify special sale for product id ".$product_id."\r\n";
            }
            else
            {
                #all good, continue
                #If this product is already associated with a special sale event, do an update
                #else do a new insert
                $sql = "select special_sale_id from special_sales_and_product where product_id = '".$product_id."'";
                $res = mysql_query($sql);
                if (! ($row = mysql_fetch_assoc($res)))
                {
                    #do an insert
                    $sql = "insert into special_sales_and_product values ('".$_POST["employee_modified2_product_radio1"]."','".$product_id."')";
                    $res = mysql_query($sql);
                    if (!$res)
                    {
                        $errmsg .= "Failed to modify special sale for product id ".$product_id."\r\n";
                    }
                }
                else
                {
                    #do an update only if 2 special sale events are different
                    if($_POST["employee_modified2_product_radio1"] != $row["special_sale_id"])
                    {
                        $sql = "update special_sales_and_product set special_sale_id = '".$_POST["employee_modified2_product_radio1"]."' where product_id = '".$product_id."'";
                        $res = mysql_query($sql);
                        if (!$res)
                        {
                            $errmsg .= "Failed to modify special sale for product id ".$product_id."\r\n";
                        }
                    }

                }
            }
        }
        disconnectDB($conn);
        #Check for error message
        if ($errmsg == '')
        {
            #No Error, woohoo!
            require "pre_employee_page.html";
            echo '<p style="color:blue">Successfully modified info for product id '.$product_id.'</p>';
            require "post_employee_page.html";
        }
        else
        {
            require "pre_employee_page.html";
            echo '<p style="color:red">'.$errmsg.'</p>';
            require "post_employee_page.html";
        }
    }
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

/*Function to display category info to be modified*/
function display_category_info_to_modify()
{
    #Validate the input once again
    $category_id = validate_data($_POST['category_modified']);
    $validate_category_id = filter_input(INPUT_POST,"category_modified",FILTER_VALIDATE_INT);
    if ($category_id == '' || $validate_category_id == NULL || $validate_category_id == false)
    {
        #Input error
        require "pre_employee_page.html";
        echo '<p style="color:red">ERROR: category id is not valid'.'</p>';
        require "post_employee_page.html";
    }
    else
    {
        $conn = connectDB();
        #Check if we have this category id in our database
        $sql = "select * from product_categories where category_id = '".$category_id."'";
        $res = mysql_query($sql);
        if (!($row = mysql_fetch_assoc($res)))
        {
            require "pre_employee_page.html";
            echo '<p style="color:red">ERROR: Could not find category id '.$category_id.' in our database.'.'</p>';
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
        <div id="employee_page_modify3_category">
            <h1><?php echo 'Catergory '.$row["category_name"].' info (category id '.$category_id.')';
        ?></h1>

            <p id="employee_page_modify3_category_errmsg" style="color:red"></p>

            <span style="font-weight: bold;position:absolute; left: 13%">Current Value</span>
            <span style="font-weight: bold; position:absolute; left: 50%">Change to value</span><br/><br/>

            <form id="modified_category_info" action="employee_page.php" method="POST">
                <!--hidden input to send server the product id that needs to be modified -->
                <input type="hidden" name="hidden_category_id" value="<?php echo $category_id; ?>"/>

            <?php
        #Category name
        echo 'Category name: ';
        echo '<span style="position:absolute; left: 13%">'.$row["category_name"].'</span>';
        echo '<input type="text" id="modified_category_name" name="modified_category_name" pattern="([a-zA-Z0-9]+)\s*([a-zA-Z0-9])*" maxlength="255" style="position:absolute; left: 50%"/><br/><br/>';

        #Category description
        echo '<p class=formfield>';
        echo 'Category description: ';
        echo '<textarea rows="30" cols="50" readonly style="position:relative; left: 5%">'.$row["category_description"].'</textarea>';
        echo '<textarea id="modified_category_description" name="modified_category_description" rows="30" cols="50" style="position:absolute; left: 50%"></textarea></p><br/>';


        echo '<button type="submit" value="go_homepage_employee">Home</button>';
        echo '<button type="submit" onclick="return validate_modify3_category()" name="mysubmit_modify3_category" value="mysubmit_modify3_category" style="position:relative; left:15px;">Submit</button>';

        ?>
            </form>
        </div>
        </body>
        </html>

        <?php
        disconnectDB($conn);
    }
}

/*Function to modify category info*/
function modify_category_info()
{
    #Validate the input once again
    $category_id = validate_data($_POST['hidden_category_id']);
    $validate_category_id = filter_input(INPUT_POST,"hidden_category_id",FILTER_VALIDATE_INT);
    if ($category_id == '' || $validate_category_id == NULL || $validate_category_id == false)
    {
        #Input error
        require "pre_employee_page.html";
        echo '<p style="color:red">ERROR: category id is not valid'.'</p>';
        require "post_employee_page.html";
    }
    else
    {
        /*Connect to our db*/
        $conn = connectDB();
        $errmsg = "";

        if($_POST['modified_category_name'] != '')
        {
            $category_name = validate_data($_POST["modified_category_name"]);
            $sql = "update product_categories set category_name='".$category_name."' where category_id='".$category_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                #Failed to update
                $errmsg .= "Failed to update category name.\r\n";
            }
        }

        if($_POST['modified_category_description'] != '')
        {
            $category_description = validate_data($_POST["modified_category_description"]);
            $sql = "update product_categories set category_description='".$category_description."' where category_id='".$category_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                #Failed to update
                $errmsg .= "Failed to update category description.\r\n";
            }
        }

        disconnectDB($conn);
        #Check for error message
        if ($errmsg == '')
        {
            #No Error, woohoo!
            require "pre_employee_page.html";
            echo '<p style="color:blue">Successfully modified info for category id '.$category_id.'</p>';
            require "post_employee_page.html";
        }
        else
        {
            require "pre_employee_page.html";
            echo '<p style="color:red">'.$errmsg.'</p>';
            require "post_employee_page.html";
        }
    }
}

/*Function to display special sale for modification*/
function display_special_sale_info_to_modify()
{
    #Validate the input once again
    $specialsale_id = validate_data($_POST['specialsale_modified']);
    $validate_specialsale_id = filter_input(INPUT_POST,"specialsale_modified",FILTER_VALIDATE_INT);
    if ($specialsale_id == '' || $validate_specialsale_id == NULL || $validate_specialsale_id == false)
    {
        #Input error
        require "pre_employee_page.html";
        echo '<p style="color:red">ERROR: special sale id is not valid'.'</p>';
        require "post_employee_page.html";
    }
    else
    {
        $conn = connectDB();
        #Check if we have this product id in our database
        $sql = "select * from special_sales where special_sale_id = '".$specialsale_id."'";
        $res = mysql_query($sql);
        if (!($row = mysql_fetch_assoc($res)))
        {
            require "pre_employee_page.html";
            echo '<p style="color:red">ERROR: Could not find special sale id '.$specialsale_id.' in our database.'.'</p>';
            require "post_employee_page.html";
            disconnectDB($conn);
            return;
        }
        #continue if we have valid special sale id
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
            <title>Special Sale info</title>
        </head>
        <body>
        <div id="employee_page_modify4_specialsale">
            <h1><?php echo 'Special sale id '.$specialsale_id.' info';
        ?></h1>

            <p id="employee_page_modify4_specialsale_errmsg" style="color:red"></p>

            <span style="font-weight: bold;position:absolute; left: 9%">Current Value</span>
            <span style="font-weight: bold; position:absolute; left: 30%">Change to value</span><br/><br/>

            <form id="modified_specialsale_info" action="employee_page.php" method="POST">
                <!--hidden input to send server the product id that needs to be modified -->
                <input type="hidden" name="hidden_specialsale_id" value="<?php echo $specialsale_id; ?>"/>

            <?php

        #Start Date
        echo 'Start date: ';
        echo '<span style="position:absolute; left: 9%">'.$row["start_date"].'</span>';
        echo '<input type="date" id="modified_specialsale_start_date" name="modified_specialsale_start_date" style="position:absolute; left: 30%"/><br/><br/>';

        #End Date
        echo 'End date: ';
        echo '<span style="position:absolute; left: 9%">'.$row["end_date"].'</span>';
        echo '<input type="date" id="modified_specialsale_end_date" name="modified_specialsale_end_date" style="position:absolute; left: 30%"/><br/><br/>';


        #Percentage discount
        echo 'Percentage discount: ';
        echo '<span style="position:absolute; left: 9%">'.$row["percentage_discount"].'</span>';
        echo '<input type="number" id="modified_specialsale_percentage_discount" name="modified_specialsale_percentage_discount" pattern="(^\d*(?:\.\d{0,2})?$)" step="0.01" min="0" max="9999" style="position:absolute; left: 30%"/><br/><br/>';

        $sql = "select product_id from special_sales_and_product where special_sale_id = '".$specialsale_id."'";
        $res = mysql_query($sql);
        $product_id_arr = array();
        $counter = 0; //This is used to check if mysql_fetch_assoc return false
        #Store returned product id into an array in case this special sale event applies to more than 1 product
        while ($row = mysql_fetch_assoc($res))
        {
            $counter += 1;
            array_push($product_id_arr,$row["product_id"]);
        }
        if ($counter == 0)
        {
            #this special sale event does not associate with any product yet
            $associated_product = "this special sale event does not associate with any product yet";
        }
        else
        {
            $associated_product = implode(',',$product_id_arr);
        }

        #Product id
        echo '<p class=formfield>';
        echo 'Product id associated'.'<br/>'.'with this special sale: ';
        echo '<textarea rows="10" cols="30" readonly style="position:relative; left: 1%">'.$associated_product.'</textarea>';

        #Now print out all products in our database for user to choose from
        $sql = "select product_id from products";
        $res = mysql_query($sql);
        if (!$res)
        {
            #database has no products
            echo '<textarea id="modified_special_sale_product" name="modified_special_sale_product" rows="30" cols="50" readonly style="position:absolute; left: 50%">Database has no products</textarea></p><br/>';
        }
        else
        {
            $checkbox_id = "employee_modified4_special_sale_cb";
            $myindex = 1;
            $initial_px = 30;
            $second_initial_px = 31;
            while($row = mysql_fetch_assoc($res))
            {
                $cb_id = $checkbox_id.$myindex;
                echo '<input type="checkbox" id='.$cb_id.' name="employee_modified4_special_sale_cb1[]" value='.$row["product_id"].' style="position:absolute; left:'.$initial_px.'%"/><span style="position:absolute; left:'.$second_initial_px.'%">'.$row["product_id"].'</span>';
                $initial_px += 5;
                $second_initial_px += 5;
                $myindex += 1;
            }
            echo '</p><br/><br/>';
        }

        echo '<button type="submit" value="go_homepage_employee">Home</button>';
        echo '<button type="submit" onclick="return validate_modify4_specialsale()" name="mysubmit_modified4_specialsale" value="mysubmit_modified4_specialsale" style="position:relative; left:15px;">Submit</button>';

        ?>
            </form>
        </div>
        </body>
        </html>

        <?php
        disconnectDB($conn);
    }
}

/*Function to modify special sale*/
function modify_special_sale_info()
{
    #Validate the input once again
    $specialsale_id = validate_data($_POST['hidden_specialsale_id']);
    $validate_specialsale_id = filter_input(INPUT_POST,"hidden_specialsale_id",FILTER_VALIDATE_INT);
    if ($specialsale_id == '' || $validate_specialsale_id == NULL || $validate_specialsale_id == false)
    {
        #Input error
        require "pre_employee_page.html";
        echo '<p style="color:red">ERROR: special sale id is not valid'.'</p>';
        require "post_employee_page.html";
    }
    else
    {
        /*Connect to our db*/
        $conn = connectDB();
        $errmsg = "";

        $sql = "select * from special_sales where special_sale_id='".$specialsale_id."'";
        $res = mysql_query($sql);
        if (!($row = mysql_fetch_assoc($res)))
        {
            require "pre_employee_page.html";
            echo '<p style="color:red">ERROR: the special sale id '.$specialsale_id.' is not found in our database'.'</p>';
            require "post_employee_page.html";
            disconnectDB($conn);
            return;
        }
        #Store original start and end date
        $orig_start_date = $row["start_date"];
        $orig_end_date = $row["end_date"];

        #Both date are modified
        if($_POST['modified_specialsale_start_date'] != '' && $_POST["modified_specialsale_end_date"] != '')
        {
            $start_date = validate_data($_POST["modified_specialsale_start_date"]);
            $end_date = validate_data($_POST["modified_specialsale_end_date"]);
            if (!check_date_before($start_date,$end_date))
            {
                #Something is wrong with either date or start date is later than end date, produce error and don't update this info
                $errmsg .= "Either start date or end date is not valid or start date is later than end date. Please double check your value\r\n";
            }
            else
            {
                $sql = "update special_sales set start_date='".$start_date."',end_date='".$end_date."' where special_sale_id='".$specialsale_id."'";
                $res = mysql_query($sql);
                if (!$res)
                {
                    #Failed to update
                    $errmsg .= "Failed to update start and end date.\r\n";
                }
            }
        }
        #Only end date is modified
        elseif($_POST['modified_specialsale_start_date'] == '' && $_POST["modified_specialsale_end_date"] != '')
        {
            #employee only modifies end date
            $end_date = validate_data($_POST["modified_specialsale_end_date"]);
            if (!check_date_before($orig_start_date,$end_date))
            {
                #Something is wrong with either date or start date is later than end date, produce error and don't update this info
                $errmsg .= "Either end date is not valid or your modified end date is before the original start date of this special sale. Please double check your value\r\n";
            }
            else
            {
                $sql = "update special_sales set end_date='".$end_date."' where special_sale_id='".$specialsale_id."'";
                $res = mysql_query($sql);
                if (!$res)
                {
                    #Failed to update
                    $errmsg .= "Failed to update end date.\r\n";
                }
            }
        }
        #Only start date is modified
        elseif($_POST['modified_specialsale_start_date'] != '' && $_POST["modified_specialsale_end_date"] == '')
        {
            #employee only modifies start date
            $start_date = validate_data($_POST["modified_specialsale_start_date"]);
            if (!check_date_before($start_date,$orig_end_date))
            {
                #Something is wrong with either date or start date is later than end date, produce error and don't update this info
                $errmsg .= "Either start date is not valid or your modified start date is after the original end date of this special sale. Please double check your value\r\n";
            }
            else
            {
                $sql = "update special_sales set start_date='".$start_date."' where special_sale_id='".$specialsale_id."'";
                $res = mysql_query($sql);
                if (!$res)
                {
                    #Failed to update
                    $errmsg .= "Failed to update start date.\r\n";
                }
            }
        }

        #Modify percentage discount
        if($_POST['modified_specialsale_percentage_discount'] != '')
        {
            $percentage_discount = validate_data_price($_POST["modified_specialsale_percentage_discount"]);
            if($percentage_discount == false)
            {
                #invalid percentage discount
                $errmsg .= "Invalid percentage discount.\r\n";
            }
            else
            {
                $sql = "update special_sales set percentage_discount='".$percentage_discount."' where special_sale_id='".$specialsale_id."'";
                $res = mysql_query($sql);
                if(!$res)
                {
                    #Error updating percentage discount
                    $errmsg .= "Failed to update percentage discount.\r\n";
                }
            }
        }

        #Modify products that are associated with this special sale
        if(isset($_POST["employee_modified4_special_sale_cb1"]))
        {
            #Check to see if the product employee wants to change actually exists in our database
            $sql = "select product_id from products";
            $res = mysql_query($sql);
            $counter = 0;
            $product_id_arr = array();
            while ($row = mysql_fetch_assoc($res))
            {
                $counter += 1;
                array_push($product_id_arr,$row["product_id"]);
            }
            if($counter == 0)
            {
                require "pre_employee_page.html";
                echo '<p style="color:red">ERROR: cannot change products associated with this special sale'.'</p>';
                require "post_employee_page.html";
                disconnectDB($conn);
                return;
            }
            #check if the input of product is actually in our database
            sort($product_id_arr);
            if(!check_subset($_POST['employee_modified4_special_sale_cb1'],$product_id_arr))
            {
                require "pre_employee_page.html";
                echo '<p style="color:red">ERROR: modifying products associated with this special sale'.'</p>';
                require "post_employee_page.html";
                disconnectDB($conn);
                return;
            }
            #Check if the employee tries to associate the products that are associating with another special sale event with this special sale
            $sql = "select product_id from special_sales_and_product where special_sale_id != '".$specialsale_id."'";
            $res = mysql_query($sql);
            $counter = 0;
            $isIn = false;
            while ($row = mysql_fetch_assoc($res))
            {
                $counter += 1;
                #Check if this value is in our product id array
                if(in_array($row["product_id"],$_POST['employee_modified4_special_sale_cb1']))
                {
                    $isIn = true;
                    break;
                }
            }
            if ($counter == 0 || $isIn == false)
            {
                #Delete the old record
                $sql = "delete from special_sales_and_product where special_sale_id ='".$specialsale_id."'";
                $res = mysql_query($sql);
                if(!$res)
                {
                    require "pre_employee_page.html";
                    echo '<p style="color:red">ERROR: modifying products associated with this special sale'.'</p>';
                    require "post_employee_page.html";
                    disconnectDB($conn);
                    return;
                }

                #Everything good, continue
                foreach ($_POST['employee_modified4_special_sale_cb1'] as $val)
                {
                    $sql = "insert into special_sales_and_product values ('".$specialsale_id."','".$val."')";
                    $res = mysql_query($sql);
                    if(!$res)
                    {
                        $errmsg .= "Error in associating product id ".$val." with this special sale. It might be due to that this product id has been associated with another special sale event. Please note that a product can only have 1 special sale event at a time\r\n";
                    }
                }
            }
            else
            {
                #Error
                $errmsg .= "ERROR: at least one of the product id that you tried to associate with special sale id ".$specialsale_id." is associated with another special sale id. Please note that a product can only be in 1 special sale at a time.\r\n";
            }
        }

        disconnectDB($conn);
        #Check for error message
        if ($errmsg == '')
        {
            #No Error, woohoo!
            require "pre_employee_page.html";
            echo '<p style="color:blue">Successfully modified info for special sale id '.$specialsale_id.'</p>';
            require "post_employee_page.html";
        }
        else
        {
            require "pre_employee_page.html";
            echo '<p style="color:red">'.$errmsg.'</p>';
            require "post_employee_page.html";
        }
    }
}

/*Function to determine if a date is before another date*/
function check_date_before ($date1, $date2)
{
    #Need to split date string into arr
    $date1_arr = explode('-',$date1);
    $date2_arr = explode('-',$date2);
    if (!checkdate($date1_arr[1],$date1_arr[2],$date1_arr[0]) || !checkdate($date2_arr[1],$date2_arr[2],$date2_arr[0]))
    {
        return false;
    }

    if($date1_arr[0] - $date2_arr[0] > 0) #Start date year is more than end date year
    {
        return false;
    }
    elseif ($date1_arr[0] - $date2_arr[0] == 0)
    {
        if ($date1_arr[1] - $date2_arr[1] > 0) #Start date month is more than end date month
        {
            return false;
        }
        elseif ($date1_arr[1] - $date2_arr[1] == 0)
        {
            if ($date1_arr[2] - $date2_arr[2] > 0) #Start date is more than end date
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            return true;
        }
    }
    else
    {
        return true;
    }
}

/*Function to delete a product from our database*/
function delete_product()
{
    #Validate the input once again
    $product_id = validate_data($_POST['delete_product']);
    $errmsg = "";
    if(filter_input(INPUT_POST,"delete_product",FILTER_VALIDATE_INT) && strlen($product_id) > 0)
    {
        $conn = connectDB();

        #Delete all the association with this product first if it exists
        #Start to remove this product from any special sale
        $sql = "delete from special_sales_and_product where product_id='".$product_id."'";
        mysql_query($sql); #Does not matter if it fails or pass since if fails, this product id is not associated with any special sale.

        #Then remove this product from any category it's in
        $sql = "delete from product_and_category where product_id='".$product_id."'";
        mysql_query($sql);

        #Now we delete the product in our database
        $sql = "delete from products where product_id='".$product_id."'";
        $res = mysql_query($sql);
        if (!$res)
        {
            $errmsg .= "Failed to delete product id ".$product_id." from our database.\r\n";
        }
        disconnectDB($conn);
    }
    else
    {
        #error
        $errmsg .= "Product id is not an integer\r\n";
    }

    if ($errmsg == '')
    {
        #No Error, woohoo!
        require "pre_employee_page.html";
        echo '<p style="color:blue">Successfully deleted product id '.$product_id.' from our database'.'</p>';
        require "post_employee_page.html";
    }
    else
    {
        require "pre_employee_page.html";
        echo '<p style="color:red">'.$errmsg.'</p>';
        require "post_employee_page.html";
    }
}

/*Function to delete category*/
function delete_category()
{
    #Validate the input once again
    $category_id = validate_data($_POST['delete_category']);

    #Make sure this category id exists
    $conn = connectDB();
    $sql = "select * from product_categories where category_id='".$category_id."'";
    $res = mysql_query($sql);
    if(!$res || !($row = mysql_fetch_assoc($res)))
    {
        require "pre_employee_page.html";
        echo '<p style="color:red">ERROR: The category id '.$category_id.' that you want to delete is not found in our database'.'</p>';
        require "post_employee_page.html";
        disconnectDB($conn);
        return;
    }

    if(filter_input(INPUT_POST,"delete_category",FILTER_VALIDATE_INT) && strlen($category_id) > 0)
    {

        #Check to see if there is any products belonging to this category
        $sql = "select product_id from product_and_category where category_id='".$category_id."'";
        $res = mysql_query($sql);
        $product_id_arr = array();
        $counter = 0;
        while($row = mysql_fetch_assoc($res))
        {
            $counter += 1;
            array_push($product_id_arr,$row["product_id"]);
        }
        if($counter == 0)
        {
            #There is no product belonging to this category or this category id is not in our database
            #Make a delete anyway.
            $sql = "delete from product_categories where category_id='".$category_id."'";
            $res = mysql_query($sql);
            if(!$res)
            {
                #error
                require "pre_employee_page.html";
                echo '<p style="color:red">Category id '.$category_id.' is not found in our database'.'</p>';
                require "post_employee_page.html";
            }
            else
            {
                #No Error, woohoo!
                require "pre_employee_page.html";
                echo '<p style="color:blue">Successfully deleted category id '.$category_id.' from our database'.'</p>';
                require "post_employee_page.html";
            }
        }
        else
        {
            $str_product_id = implode(',',$product_id_arr);
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
                <title>Product id(s) in category</title>
            </head>
            <body>
            <div id="employee_page_delete3_product_id_category">
                <h1><?php echo 'Product id(s) belonging to category id '.$category_id;
                    ?></h1>

                <p id="employee_page_delete3_product_id_category_errmsg" style="color:red"></p>

                <form id="employee_page_delete3_product_id_category_form_id" action="employee_page.php" method="POST">
                    <!--hidden input to send server the product id that needs to be modified -->
                    <input type="hidden" name="hidden_product_id_arr" value="<?php echo $str_product_id; ?>"/>
                    <input type="hidden" name="hidden_category_id_to_delete" value="<?php echo $category_id; ?>"/>


            <?php
            echo '<p class=formfield>';
            echo 'Product id(s) belonging to this category is: ';
            echo '<textarea rows="10" cols="30" readonly style="position:relative; left: 1%">'.$str_product_id.'</textarea></p><br/>';
            echo 'You have to remove all these products from our database before you can delete this category id. (exceptions is when the product id(s) belong to other category, then you do not need to remove it. We will handle this automatically in our system)<br/>Do you want to continue? NOTE: this step will remove all products in this category and then remove this category from our database.<br/>';

            echo '<button type="submit" name="delete_products_from_category_yes" value="delete_products_from_category_yes">Yes</button>';
            echo '<button type="submit" name="delete_products_from_category_no" value="delete_products_from_category_no" style="position:relative; left:15px;">No</button>';

            ?>
                </form>
            </div>
            </body>
            </html>

        <?php
        }
    }
    else
    {
        require "pre_employee_page.html";
        echo '<p style="color:red">Category id '.$category_id.' is not an integer'.'</p>';
        require "post_employee_page.html";
    }
    disconnectDB($conn);
}

/*Function to delete all products in a category before deleting the category itself*/
function delete_all_product_delete_category()
{
    $category_id = validate_data($_POST["hidden_category_id_to_delete"]);
    $errmsg = '';

    #Make sure this category id exists
    $conn = connectDB();
    $sql = "select * from product_categories where category_id='".$category_id."'";
    $res = mysql_query($sql);
    if(!$res || !($row = mysql_fetch_assoc($res)))
    {
        require "pre_employee_page.html";
        echo '<p style="color:red">ERROR: The category id '.$category_id.' that you want to delete is not found in our database'.'</p>';
        require "post_employee_page.html";
        disconnectDB($conn);
        return;
    }

    if(($_POST["hidden_product_id_arr"]) != '')
    {
        $product_id_arr = explode(',',$_POST["hidden_product_id_arr"]);
        #Check if these product id exists in our database
        $sql = "select product_id from products";
        $res = mysql_query($sql);
        $counter = 0;
        $all_product_id_arr = array();
        while($row = mysql_fetch_assoc($res))
        {
            $counter += 1;
            array_push($all_product_id_arr,$row["product_id"]);
        }
        if($counter == 0)
        {
            #return error
            require "pre_employee_page.html";
            echo '<p style="color:red">Fatal error: Cannot retrieve any product id from our database'.'</p>';
            require "post_employee_page.html";
            disconnectDB($conn);
            return;
        }
        sort($all_product_id_arr);
        if(!check_subset($product_id_arr,$all_product_id_arr))
        {
            #return error
            require "pre_employee_page.html";
            echo '<p style="color:red">Fatal error: at least one product id reported to be in the category id '.$category_id.' is not found in our product table'.'</p>';
            require "post_employee_page.html";
            disconnectDB($conn);
            return;
        }
        #everything good, continue
        #Only delete product that ONLY belongs to this category id. If the product belong to this category and
        #some other category id(s), leave this product alone since it won't create NULL enties in our table
        $arr_product_id_not_in_this_category = array();
        $sql = "select product_id from product_and_category where category_id != '".$category_id."'";
        $res = mysql_query($sql);
        $counter = 0;
        while ($row = mysql_fetch_assoc($res))
        {
            $counter += 1;
            array_push($arr_product_id_not_in_this_category,$row["product_id"]);
        }

        #This mean product_and_category table is either empty or only has this category id
        #Start to delete all products before delete category
        foreach($product_id_arr as $val)
        {
            if($counter != 0)
            {
                if(!in_array($val,$arr_product_id_not_in_this_category))
                {
                    if (!delete_product_basedOn_productID($val))
                    {
                        $errmsg .= "Cannot remove this product id".$val." from our database\r\n";
                    }
                }
            }
            else
            {
                if (!delete_product_basedOn_productID($val))
                {
                    $errmsg .= "Cannot remove this product id".$val." from our database\r\n";
                }
            }
        }
        if ($errmsg == '')
        {
            #Call to delete product-category relationship in product_and_category
            $sql = "delete from product_and_category where category_id='".$category_id."'";
            mysql_query($sql); #Does not matter it fails or not. If it fails, means that this action is already taken care of in previous step.

            #remove the category itself
            $sql = "delete from product_categories where category_id='".$category_id."'";
            $res = mysql_query($sql);
            if (!$res)
            {
                $errmsg .= "1Failed to delete category id ".$category_id;
            }
        }
    }
    else
    {
        #error
        $errmsg .= "2Failed to delete category id ".$category_id;
    }

    disconnectDB($conn);
    if ($errmsg == '')
    {
        #No Error, woohoo!
        require "pre_employee_page.html";
        echo '<p style="color:blue">Successfully deleted category id '.$category_id.' from our database'.'</p>';
        require "post_employee_page.html";
    }
    else
    {
        require "pre_employee_page.html";
        echo '<p style="color:red">'.$errmsg.'</p>';
        require "post_employee_page.html";
    }
}

/*Delete product based on product id*/
function delete_product_basedOn_productID( $pid )
{
    #Validate the input once again
    $product_id = validate_data($pid);
    $errmsg = "";
    if(strlen($product_id) > 0)
    {
        /*Don't need to connect db since we already did that in original function call*/

        #Delete all the association with this product first if it exists
        #Start to remove this product from any special sale
        $sql = "delete from special_sales_and_product where product_id='".$product_id."'";
        mysql_query($sql); #Does not matter if it fails or pass since if fails, this product id is not associated with any special sale.

        #Then remove this product from any category it's in
        $sql = "delete from product_and_category where product_id='".$product_id."'";
        mysql_query($sql);

        #Now we delete the product in our database
        $sql = "delete from products where product_id='".$product_id."'";
        $res = mysql_query($sql);
        if (!$res)
        {
            $errmsg .= "Failed to delete product id ".$product_id." from our database.\r\n";
        }
    }
    else
    {
        #error
        $errmsg .= "Product id is not an integer\r\n";
    }

    if ($errmsg == '')
    {
        #No Error, woohoo!
        return true;
    }
    else
    {
        return false;
    }
}

/*Function to delete special sale*/
function delete_special_sale()
{
    #Validate the input once again
    $specialsale_id = validate_data($_POST['delete_specialsale']);
    $errmsg = "";

    #Make sure this special sale id exists
    $conn = connectDB();
    $sql = "select * from special_sales where special_sale_id='".$specialsale_id."'";
    $res = mysql_query($sql);
    if(!$res || !($row = mysql_fetch_assoc($res)))
    {
        require "pre_employee_page.html";
        echo '<p style="color:red">ERROR: The special sale id '.$specialsale_id.' that you want to delete is not found in our database'.'</p>';
        require "post_employee_page.html";
        disconnectDB($conn);
        return;
    }

    if(filter_input(INPUT_POST,"delete_specialsale",FILTER_VALIDATE_INT) && strlen($specialsale_id) > 0)
    {
        #Delete the special sale - product relationship in special_sales_and_product table
        $sql = "delete from special_sales_and_product where special_sale_id='".$specialsale_id."'";
        mysql_query($sql); #Does not matter if this function return true or false. If false, it means that the special sale we are trying to delete does not associate with any product

        #Delete the special sale id itself in the table
        $sql = "delete from special_sales where special_sale_id='".$specialsale_id."'";
        $res = mysql_query($sql);
        if(!$res)
        {
            $errmsg .= "Failed to delete special sale id ".$specialsale_id." from our database.\r\n";
        }
    }
    else
    {
        $errmsg .= "Special sale id ".$specialsale_id." is not an integer.\r\n";
    }

    disconnectDB($conn);
    if ($errmsg == '')
    {
        #No Error, woohoo!
        require "pre_employee_page.html";
        echo '<p style="color:blue">Successfully deleted special sale id '.$specialsale_id.' from our database'.'</p>';
        require "post_employee_page.html";
    }
    else
    {
        require "pre_employee_page.html";
        echo '<p style="color:red">'.$errmsg.'</p>';
        require "post_employee_page.html";
    }
}

?>