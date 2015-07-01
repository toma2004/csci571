<?php
/**
 * Created by PhpStorm.
 * User: NguyenTran
 * Date: 6/30/2015
 * Time: 9:39 PM
 */

if (isset($_POST["special_sale_display"]))
{
    display_special_sale_main_page();
}
else if (isset($_POST["product_name_clicked"]))
{
    display_detail_product($_POST["product_name_clicked"]);
}
else
{
    require "main_webpage.html";
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

/*Function to display special sale items on main page*/
function display_special_sale_main_page()
{
    $conn = connectDB();

    /*Check to see if there is any product on sale*/
    $sql = "select special_sale_id, product_id from special_sales_and_product";
    $res = mysql_query($sql);
    if (!$res)
    {
        #There is no product on sale
        echo '<p style="color: red"> There is no product on sale at the moment</p>';
    }
    else
    {
        while($row = mysql_fetch_assoc($res))
        {
            /*Getting image and price*/
            $sql = "select product_name,product_price,product_image from products where product_id='".$row["product_id"]."'";
            $res_product = mysql_query($sql);
            if($res_product)
            {
                $row_product = mysql_fetch_assoc($res_product);
                /*Getting special sale percentage discount*/
                $sql = "select percentage_discount from special_sales where special_sale_id='".$row["special_sale_id"]."'";
                $res_special_sale = mysql_query($sql);
                if($res_special_sale)
                {
                    $row_special_sale = mysql_fetch_assoc($res_special_sale);

                    /*start to display it*/
                    echo '<div class="image_block">';
                    echo '<input type="image" src="'.$row_product["product_image"].'" alt="'.$row_product["product_name"].'" style="width: 200px; height: 200px;" name="product_name_clicked" value="'.$row["product_id"].'">';
                    echo '<span class="caption"><span style="color: red">SALE: '.$row_special_sale["percentage_discount"].' %OFF</span><br/>';
                    echo 'Orig. price = '.$row_product["product_price"].'<br/>';
                    $discounted = (1 - ($row_special_sale["percentage_discount"] / 100)) * $row_product["product_price"];
                    echo '<span style="color: red">Discounted price = '.$discounted.'</span></span></div>';
                }
            }
        }
    }
    disconnectDB($conn);
}

/*Function to display a detail of product given product id*/
function display_detail_product( $product_id )
{
    $conn = connectDB();

    /*retrieve product info*/
    $sql = "select product_name, product_description, ingredients, product_image from products where product_id ='".$product_id."'";
    $res = mysql_query($sql);
    if(!$res)
    {
        echo '<p style="color: red"> ERROR: retrieving product information</p>';
    }
    else
    {
        if(! ($row = mysql_fetch_assoc($res)))
        {
            echo '<p style="color: red"> ERROR: There is no product found</p>';
        }
        else
        {
            /*Check category before displaying*/
            $sql = "select category_id from product_and_category where product_id = '".$product_id."'";
            $res_category = mysql_query($sql);
            $counter = 0;
            $category_id_arr = array();
            while ($row_category = mysql_fetch_assoc($res_category))
            {
                $counter += 1;
                array_push($category_id_arr,$row_category["category_id"]);
            }
            if ($counter == 0)
            {
                echo '<p style="color: red"> ERROR: There is no product category found</p>';
            }
            else
            {
                $category_name_str = '';
                $category_name_arr = array();
                foreach ($category_id_arr as $val)
                {
                    $sql = "select category_name from product_categories where category_id = '".$val."'";
                    $res_category_info = mysql_query($sql);
                    if (! ($row_category_info = mysql_fetch_assoc($res_category_info)))
                    {
                        echo '<p style="color: red"> ERROR: retrieving product category information</p>';
                        disconnectDB($conn);
                        return;
                    }
                    else
                    {
                        array_push($category_name_arr,$row_category_info["category_name"]);
                    }
                }
                $category_name_str = implode(",", $category_name_arr);

                if ($category_name_str == '')
                {
                    echo '<p style="color: red"> ERROR: There is no product category associated with this product</p>';
                }
                else
                {
                    ?>

                    <!-- End php and display html -->
                    <!DOCTYPE html>
                    <html>
                    <head lang="en">
                        <meta charset="UTF-8"/>
                        <meta name="author" content="Nguyen Tran"/>

                        <link rel="stylesheet" type="text/css" href="main_css.css"/> <!-- link to external css file
                                <!--<link rel="shortcut icon" type="image/jpg" href="flower_crown1.jpg"></link> -->
                        <title>Product info</title>
                    </head>
                    <body>
                        <form id="display_product_info" action="main_page.php" method="POST">
                    <?php
                    /*start to display product info*/
                    echo '<img src="'.$row["product_image"].'" alt="'.$row["product_name"].'" style="width: 400px; height: 200px;"><br/>';

                    echo 'Product name: ';
                    echo '<span style="position:absolute; left: 13%">'.$row["product_name"].'</span><br/>';

                    echo '<p class="display_product_info">';
                    echo 'Product description: ';
                    echo '<textarea rows="20" cols="30" readonly style="position:relative; left: 5%">'.$row["product_description"].'</textarea>';

                    echo '<span style="position: absolute; left: 40%">Ingredient: </span>';
                    echo '<textarea rows="20" cols="30" readonly style="position: absolute; left: 50%">'.$row["ingredients"].'</textarea></p><br/>';

                    echo '<p class="display_product_info">';
                    echo 'Belong to category: ';
                    echo '<textarea rows="20" cols="30" readonly style="position:relative; left: 5.5%">'.$category_name_str.'</textarea></p><br/>';

                    /*See if this product has any special sale. If yes, display that info too*/
                    $sql = "select special_sale_id from special_sales_and_product where product_id = '".$product_id."'";
                    $res_special_sale = mysql_query($sql);
                    if ($res_special_sale)
                    {
                        if ($row_special_sale = mysql_fetch_assoc($res_special_sale))
                        {
                            $sql = "select start_date, end_date, percentage_discount from special_sales where special_sale_id = '".$row_special_sale["special_sale_id"]."'";
                            $res_special_sale_info = mysql_query($sql);
                            if ($row_special_sale_info = mysql_fetch_assoc($res_special_sale_info))
                            {
                                echo 'This product '.$row["product_name"]. ' has special sale event discount at <span style="color: red">'.$row_special_sale_info["percentage_discount"].'% from '.$row_special_sale_info["start_date"].' to '.$row_special_sale_info["end_date"].'</span><br/>';
                            }
                        }
                    }
                    echo 'ACT FAST and ORDER YOURS TODAY WHILE SUPPLIES LAST!<br/><br/>';

                    echo '<button type="submit">Home</button>';
                    echo '<button type="button" style="position:relative; left:15px;">Add to Cart</button>';
                    ?>
                            </form>
                    </body>
                    </html>

                    <?php
                }
            }
        }
    }
    disconnectDB($conn);
}

?>