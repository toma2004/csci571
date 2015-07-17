<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: NguyenTran
 * Date: 7/12/2015
 * Time: 12:09 AM
 */

class Main_webpage extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('html_helper');
        $this->load->helper('url');
        $this->load->model('main_page_model');
        $this->load->model('products_model');
        $this->load->model('user_account_model');
        $this->load->model('shopping_cart_model');
        $this->load->model('order_model');
        /*Establish session with username and password*/
        if(!isset($_SESSION))
        {
            session_start();
        }
    }

    /*Default function to display main webpage when customer connects to the site*/
    public function index( $err_msg='none' )
    {
        $data_to_main_page_view['special_sale_display'] = $this->main_page_model->get_special_sale_display();
        $data_to_main_page_view['category_list'] = $this->main_page_model->get_category_dropDown_list();
        if ($err_msg != 'none')
        {
            if ($err_msg == 'Your order has been placed successfully. Thank you very much!')
            {
                $data_to_main_page_view['place_order_successful'] = $err_msg;
            }
            else
            {
                $data_to_main_page_view['err_msg'] = $err_msg;
            }
        }
        //Load view
        $this->load->view('main_page_view', $data_to_main_page_view);
    }

    /*Function to get detail of a product*/
    public function get_product_detail()
    {
        if (!$this->has_session_timeout())
        {
            $data_detail['detail_product_info'] = $this->products_model->get_product_detail($this->input->post('product_name_clicked'));
            $this->load->view('product_detail_view', $data_detail);
        }
    }

    /*Function to response from Add_To_Cart button*/
    public function response_product_detail_page()
    {
        if ($this->input->post('to_home') != NULL)
        {
            $this->index();
        }
        //need to handle add to cart
        else if ($this->input->post('add_to_cart') != NULL)
        {
            /*Check if there is a session array*/
            if (isset($_SESSION))
            {
                //Go to model to get all info about the product being added
                $product_info = $this->shopping_cart_model->get_product_info_being_addedToCart($this->input->post('hidden_add_to_cart_pid'));
                if ($product_info == 'false')
                {
                    $this->index('ERROR: adding product to your cart');
                }
                else
                {
                    /*Check if a shopping cart already exists. If not create one*/
                    if (isset($_SESSION["shopping_cart"]))
                    {
                        $index = -1;
                        #There is a shopping card.
                        #Now check if this product already exists in the cart
                        foreach ($_SESSION["shopping_cart"] as $i => $cart_items)
                        {
                            if ($cart_items["pid"] == $this->input->post('hidden_add_to_cart_pid'))
                            {
                                $index = $i;
                                break;
                            }
                        }
                        /*If the product does not exist, add this new product to our shopping cart array*/
                        if ($index == -1)
                        {
                            array_push($_SESSION["shopping_cart"], array("qty" => "1",
                                                                         "pid" => $this->input->post('hidden_add_to_cart_pid'),
                                                                         "product_name" => $product_info['product_name'],
                                                                         "product_price" => $product_info['product_price'],
                                                                         "product_image" => $product_info['product_image'],
                                                                         "isOnSale" => $product_info['isOnSale'],
                                                                         "special_sale_id" => $product_info['special_sale_id'],
                                                                         "discounted" => $product_info['discounted']));
                        }
                        else
                        {
                            /*Product already exists. Update the quantity*/
                            $_SESSION["shopping_cart"][$index]["qty"] += 1;
                        }
                    }
                    else
                    {
                        #This is the first time user add a product to a cart. Create shopping cart
                        $_SESSION["shopping_cart"][] = array("qty" => "1",
                                                             "pid" => $this->input->post('hidden_add_to_cart_pid'),
                                                             "product_name" => $product_info['product_name'],
                                                             "product_price" => $product_info['product_price'],
                                                             "product_image" => $product_info['product_image'],
                                                             "isOnSale" => $product_info['isOnSale'],
                                                             "special_sale_id" => $product_info['special_sale_id'],
                                                             "discounted" => $product_info['discounted']);
                    }
                    $this->index('Successfully added item to your cart');
                }
            }
        }
    }

    /*Function to display shopping cart*/
    public function display_shopping_cart()
    {
        if ($this->input->post('display_shopping_cart') != NULL)
        {
            //Load view to display cart info
            $this->load->view('ajax_response_cart_info', $_SESSION);
        }
    }

    /*Function to change quantity of products in cart*/
    public function change_quantity_product_cart()
    {
        if ($this->input->post('change_shopping_cart_product_id') != NULL && $this->input->post('quantity') != NULL)
        {
            $product_id = $this->main_page_model->validate_data($this->input->post('change_shopping_cart_product_id'), "int");
            $qty = $this->main_page_model->validate_data($this->input->post('quantity'), "int");
            if ($product_id == false || $qty == false)
            {
                $data_to_view['change_quantity_product_cart'] = 'fail';
            }
            else
            {
                if (isset($_SESSION["shopping_cart"]))
                {
                    $index = $this->index_productID_shopping_cart ($product_id);
                    if ($index == -1)
                    {
                        /*Strange error where we could not find the product in our shopping cart*/
                        $data_to_view['change_quantity_product_cart'] = 'fail';
                    }
                    else
                    {
                        $_SESSION["shopping_cart"][$index]["qty"] = $qty;
                        $data_to_view['change_quantity_product_cart'] = 'true';
                    }
                }
                else
                {
                    $data_to_view['change_quantity_product_cart'] = 'fail';
                }
            }
            $this->load->view('ajax_response_cart_info', $data_to_view);
        }
    }

    /*Function to remove a product in shopping cart*/
    public function delete_product_cart()
    {
        if ($this->input->post('remove_item_product_id') != NULL)
        {
            $remove_pid = $this->main_page_model->validate_data($this->input->post('remove_item_product_id'), "int");
            if ($remove_pid == false)
            {
                $data_to_view['delete_product_cart'] = 'fail';
            }
            else
            {
                if (isset($_SESSION["shopping_cart"]))
                {
                    $index = $this->index_productID_shopping_cart ($remove_pid);
                    if ($index == -1)
                    {
                        /*Strange error where we could not find the product in our shopping cart*/
                        $data_to_view['delete_product_cart'] = 'fail';
                    }
                    else
                    {
                        /*remove this product id from our shopping cart*/
                        unset($_SESSION["shopping_cart"][$index]);
                        $data_to_view['delete_product_cart'] = 'success';
                    }
                }
                else
                {
                    $data_to_view['delete_product_cart'] = 'fail';
                }
            }
            $this->load->view('ajax_response_cart_info', $data_to_view);
        }
    }

    /*Function to remove entire shopping cart*/
    public function remove_entire_shopping_cart()
    {
        if ($this->input->post('remove_entire_cart') != NULL)
        {
            $remove_cart = $this->main_page_model->validate_data($this->input->post('remove_entire_cart'), "int");
            if ($remove_cart == false)
            {
                $data_to_view['delete_cart'] = 'fail';
            }
            else
            {
                if (isset($_SESSION["shopping_cart"]))
                {
                    foreach ($_SESSION["shopping_cart"] as $i => $cart_items)
                    {
                        unset ($cart_items["pid"]);
                        unset ($cart_items["qty"]);
                        unset ($cart_items["product_name"]);
                        unset ($cart_items["product_price"]);
                        unset ($cart_items["product_image"]);
                        unset ($cart_items["isOnSale"]);
                        unset ($cart_items["discounted"]);
                        unset ($cart_items["special_sale_id"]);
                        unset ($_SESSION["shopping_cart"][$i]);
                    }
                    $data_to_view['delete_cart'] = 'success';
                }
                else
                {
                    $data_to_view['delete_cart'] = 'fail';
                }
            }
            $this->load->view('ajax_response_cart_info', $data_to_view);
        }
    }

    /*Function to check out cart*/
    public function check_out_cart()
    {
        if ($this->input->post('request_for_checkout') != NULL)
        {
            $sanity_check = $this->main_page_model->validate_data($this->input->post('request_for_checkout'), "int"); //should be equal to 1 and hence it's an integer
            if ($sanity_check != false)
            {
                //check if customer logs in. They need to log in before checking out
                if (isset($_SESSION["username"]) && isset($_SESSION["password"]) && isset($_SESSION["log_in_successfully"]))
                {
                    if (isset($_SESSION["shopping_cart"]) && count($_SESSION["shopping_cart"]) > 0)
                    {
                        $hasGotInfo = $this->user_account_model->get_customer_info( $_SESSION['cus_id'] );
                        if ($hasGotInfo != 'false')
                        {
                            $data_to_view['customer_info_checkout'] = $hasGotInfo;
                            $data_to_view['shopping_cart_info_checkout'] = $_SESSION["shopping_cart"];
                        }
                    }
                    else
                    {
                        $data_to_view['cart_empty_checkout'] = '1';
                    }
                    $this->load->view('check_out_summary', $data_to_view);
                }
                else
                {
                    $data_to_view['need_log_in_before_checkout'] = '1';
                    $this->load->view('check_out_summary', $data_to_view);
                }
            }
        }
    }

    /*Function to place order*/
    public function place_order()
    {
        //Check if customer tries to log in before checking out
        if ($this->input->post('submit_log_in') != NULL)
        {
            $this->log_in();
        }
        //Customer wants to edit profile before checking out
        else if ($this->input->post('edit_profile_from_checkout') != NULL)
        {
            if (!$this->has_session_timeout())
            {
                $this->display_profile_to_edit();
            }
        }
        //Customer want to edit cart before placing order
        else if ($this->input->post('edit_profile_from_checkout') != NULL)
        {
            $this->display_shopping_cart();
        }
        //Most important action, customer actually placed the order
        else if ($this->input->post('place_order'))
        {
            if (!$this->has_session_timeout())
            {
                //check to see if we have total amount numbers
                $order_total_amount = $this->input->post('hidden_order_total_amount');
                $order_total_tax = $this->input->post('hidden_order_total_tax');
                $order_total_shipping = $this->input->post('hidden_order_total_shipping');
                if ($order_total_amount == NULL || $order_total_tax == NULL || $order_total_shipping == NULL)
                {
                    $this->index('ERROR: placing your order. Please try again');
                }
                else
                {
                    $hasPlacedOrder = $this->order_model->place_order($_SESSION["shopping_cart"], $order_total_amount, $order_total_tax, $order_total_shipping, $_SESSION["cus_id"]);
                    if ($hasPlacedOrder == 'success')
                    {
                        //placing order successfully. Clear shopping cart
                        foreach ($_SESSION["shopping_cart"] as $i => $cart_items)
                        {
                            unset ($cart_items["pid"]);
                            unset ($cart_items["qty"]);
                            unset ($cart_items["product_name"]);
                            unset ($cart_items["product_price"]);
                            unset ($cart_items["product_image"]);
                            unset ($cart_items["isOnSale"]);
                            unset ($cart_items["discounted"]);
                            unset ($cart_items["special_sale_id"]);
                            unset ($_SESSION["shopping_cart"][$i]);
                        }
                        $this->index('Your order has been placed successfully. Thank you very much!');
                    }
                    else
                    {
                        $this->index('ERROR: placing your order. Please try again');
                    }
                }
            }
        }
    }

    /*Function to display past order*/
    public function display_past_order()
    {
        if (!$this->has_session_timeout())
        {
            if ($this->input->post('request_for_past_order') != NULL)
            {
                $hasGotOrderInfo =  $this->order_model->get_all_orders_info($_SESSION['cus_id']);
                if ($hasGotOrderInfo == 'fail')
                {
                    $data_to_view['err'] = 'fail';
                }
                else if ($hasGotOrderInfo == 'no_order')
                {
                    $data_to_view['no_order'] = 'no_order';
                }
                else
                {
                    $data_to_view['order_info'] = $hasGotOrderInfo;
                }
                $this->load->view('ajax_response_past_order_view', $data_to_view);
            }
        }
    }

    /*Function to display detail of a specific past order*/
    public function display_detail_past_order()
    {
        if (!$this->has_session_timeout())
        {
            if ($this->input->post('request_past_order_detail') != NULL)
            {
                $order_detail = $this->order_model->get_order_detail($this->input->post('request_past_order_detail'));
                $order_detail_item = $this->order_model->get_order_items_detail($this->input->post('request_past_order_detail'));
                //Need to check error
            }
        }
    }

    /*Function to return an index of array that contain the product id we are looking for in a shopping cart*/
    protected function index_productID_shopping_cart ( $product_id )
    {
        if (isset($_SESSION["shopping_cart"]))
        {
            $index = -1;
            foreach ($_SESSION["shopping_cart"] as $i => $cart_items)
            {
                if ($cart_items["pid"] == $product_id)
                {
                    $index = $i;
                    break;
                }
            }
            return $index;
        }
        else
        {
            return -1;
        }
    }

    /*Function to display all products under a category*/
    public function display_products_in_category()
    {
        if (!$this->has_session_timeout())
        {
            $data_detail['products_in_category'] = $this->products_model->get_products_in_category($this->input->post('display_category_id'));
            $this->load->view('products_in_category_div', $data_detail); //This view is an AJAX response
        }
    }

    /*Function to handle sign up process*/
    public function user_sign_up()
    {
        $data_pass_to_view = array();
        if ($this->input->post('sign_up_user_name') != NULL)
        {
            //AJAX request to check if user name is OK with no duplicate in our database
            $data_pass_to_view['username_check'] = $this->user_account_model->checkUnique($this->input->post('sign_up_user_name'), 'user_name');
        }
        if ($this->input->post('sign_up_email') != NULL)
        {
            //AJAX request to check if user name is OK with no duplicate in our database
            $data_pass_to_view['email_check'] = $this->user_account_model->checkUnique($this->input->post('sign_up_email'), 'email');
        }
        if ($this->input->post('submit_sign_up_form') != NULL)
        {
            $data_pass_to_view['result_add_new_user'] = $this->user_account_model->add_new_customer($this->input->post('first_name'), $this->input->post('last_name'),$this->input->post('addr_shipping'),$this->input->post('city_shipping'),$this->input->post('state_shipping'),$this->input->post('country_shipping'),$this->input->post('dob'),$this->input->post('mycreditcard'),$this->input->post('mysecuritycode'),$this->input->post('myexpiredate_month'),$this->input->post('myexpiredate_year'),$this->input->post('addr_billing'),$this->input->post('city_billing'),$this->input->post('state_billing'), $this->input->post('country_billing'), $this->input->post('phone'), $this->input->post('email_addr'), $this->input->post('usr'), $this->input->post('pass'));
        }
        $this->load->view('ajax_response_sign_up_form',$data_pass_to_view);
    }

    /*function to log in*/
    public function log_in()
    {
        $un = $this->input->post('user_name');
        $pwd = $this->input->post('pass_word');
        if ($this->input->post('submit_log_in') != NULL && $un != NULL && $pwd != NULL)
        {
            $data_to_view["log_in_check"] = $this->user_account_model->check_log_in($un,$pwd);
            if ($data_to_view["log_in_check"] != 'false')
            {
                #store session info
                $_SESSION['username'] = $un;
                $_SESSION['password'] = $pwd;
                $_SESSION['cus_id'] = $data_to_view["log_in_check"];
                $_SESSION['last_activity'] = time();
                $_SESSION['timeout'] = 0;

                /*Check if customer already has something in the cart.
                * If yes, "merge" them with what they had last time signed in
                * If not, populate a shopping cart session array
                */
                $isShoppingCart = false;
                $isFirst = 0;
                if (isset($_SESSION["shopping_cart"]))
                {
                    $isShoppingCart = true;
                }

                $shopping_cart_info = $this->shopping_cart_model->get_cart_info($_SESSION['cus_id']);

                if ($shopping_cart_info != 'false')
                {
                    //Merge what they have to their current shopping cart prior logging in
                    foreach ($shopping_cart_info as $stored_cart_item)
                    {
                        /*Case where customer has done something with shopping cart before logging in*/
                        if ($isShoppingCart)
                        {
                            $index = -1;
                            #There is a shopping card.
                            #Now check if this product already exists in the cart
                            foreach ($_SESSION["shopping_cart"] as $i => $cart_items)
                            {
                                if ($cart_items["pid"] == $stored_cart_item["product_id"])
                                {
                                    $index = $i;
                                    break;
                                }
                            }
                            /*If the product does not exist, add this new product to our shopping cart array*/
                            if ($index == -1)
                            {
                                //Get product info to add to cart
                                $product_info = $this->shopping_cart_model->get_product_info_being_addedToCart($stored_cart_item["product_id"]);
                                array_push($_SESSION["shopping_cart"], array("qty" => $stored_cart_item["quantity"],
                                                                             "pid" => $stored_cart_item["product_id"],
                                                                             "product_name" => $product_info['product_name'],
                                                                             "product_price" => $product_info['product_price'],
                                                                             "product_image" => $product_info['product_image'],
                                                                             "isOnSale" => $product_info['isOnSale'],
                                                                             "special_sale_id" => $product_info['special_sale_id'],
                                                                             "discounted" => $product_info['discounted']));
                            }
                            else
                            {
                                /*Product already exists. Update the quantity*/
                                $_SESSION["shopping_cart"][$index]["qty"] += $stored_cart_item["quantity"];
                            }
                        }
                        /*Case where customer has NOT done anything before logging in*/
                        else
                        {
                            $product_info = $this->shopping_cart_model->get_product_info_being_addedToCart($stored_cart_item["product_id"]);
                            if ($isFirst == 0)
                            {
                                $_SESSION["shopping_cart"][] = array("qty" => $stored_cart_item["quantity"],
                                                                     "pid" => $stored_cart_item["product_id"],
                                                                     "product_name" => $product_info['product_name'],
                                                                     "product_price" => $product_info['product_price'],
                                                                     "product_image" => $product_info['product_image'],
                                                                     "isOnSale" => $product_info['isOnSale'],
                                                                     "special_sale_id" => $product_info['special_sale_id'],
                                                                     "discounted" => $product_info['discounted']);
                                $isFirst += 1;
                            }
                            else
                            {
                                array_push($_SESSION["shopping_cart"], array("qty" => $stored_cart_item["quantity"],
                                                                             "pid" => $stored_cart_item["product_id"],
                                                                             "product_name" => $product_info['product_name'],
                                                                             "product_price" => $product_info['product_price'],
                                                                             "product_image" => $product_info['product_image'],
                                                                             "isOnSale" => $product_info['isOnSale'],
                                                                             "special_sale_id" => $product_info['special_sale_id'],
                                                                             "discounted" => $product_info['discounted']));
                            }
                        }
                    }
                }
                //Load view to main page
                $data_to_view['special_sale_display'] = $this->main_page_model->get_special_sale_display();
                $data_to_view['category_list'] = $this->main_page_model->get_category_dropDown_list();
                $_SESSION['log_in_successfully'] = '1';
                //Load view
                $this->load->view('main_page_view', $data_to_view);
            }
            else
            { //Invalid log in
                $data_to_view['failed_log_in'] = '1';
                $this->load->view('log_in_form_view', $data_to_view);
            }
        }
    }

    /*Function to log out*/
    public function log_out()
    {
        $data_to_view['errmsg_logout'] = '';
        if (isset($_SESSION['timeout']))
        {
            if ($_SESSION['timeout'] == "1")
            {
                $data_to_view['errmsg_logout'] .= "Your session is timeout. Please log back in";
            }
            else
            {
                $data_to_view['errmsg_logout'] .= "You have successfully logged out";
            }
        }
        else
        {
            $data_to_view['errmsg_logout'] .= "You have successfully logged out";
        }
        /*As customer log out, we will save his/her shopping cart for next time visit*/
        /*Before saving new shopping cart for this customer, remove the old ones*/
        /*As customer log out, we will save his/her shopping cart for next time visit*/
        if (isset($_SESSION["shopping_cart"]) && isset($_SESSION["cus_id"]) && isset($_SESSION["log_in_successfully"]))
        {
            $has_deleted_successfully = $this->shopping_cart_model->delete_items_cart($_SESSION["cus_id"]);
            if ($has_deleted_successfully == 'true')
            {
                $this->shopping_cart_model->insert_cart_to_database($_SESSION["cus_id"]);
            }
        }

        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies"))
        {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        $data_to_view['special_sale_display'] = $this->main_page_model->get_special_sale_display();
        $data_to_view['category_list'] = $this->main_page_model->get_category_dropDown_list();
        $this->load->view('main_page_view', $data_to_view);
    }

    /*Function to check if session is time out.
    * If yes, automatically log out
    * If no, reset time out
    */
    protected function has_session_timeout()
    {
        if (isset($_SESSION['last_activity']) && isset($_SESSION['timeout']))
        {
            $t = time();
            if (($t - $_SESSION['last_activity']) > 1800)
            {
                $_SESSION['timeout'] = 1;
                $this->log_out();
                return true;
            }
            else
            {
                #session is not yet timeout. Reset time to give customer another 30 mins
                $_SESSION['last_activity'] = time();
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /*Function to edit profile*/
    public function display_profile_to_edit()
    {
        if (!$this->has_session_timeout())
        {
            //Check if user log in properly
            if (isset($_SESSION["log_in_successfully"]) && isset($_SESSION["username"]) && isset($_SESSION["password"]))
            {
                //Getting info from model/server to display
                $data_to_view['customer_info'] = $this->user_account_model->get_customer_info($_SESSION["cus_id"]);
                if ($data_to_view['customer_info'] == 'false')
                {
                    $data_to_main_page_view['special_sale_display'] = $this->main_page_model->get_special_sale_display();
                    $data_to_main_page_view['category_list'] = $this->main_page_model->get_category_dropDown_list();
                    $data_to_main_page_view['fail_edit'] = '1';
                    //Load view
                    $this->load->view('main_page_view', $data_to_main_page_view);
                }
                else
                {
                    //everything goes well
                    $this->load->view('edit_profile_view', $data_to_view);
                }
            }
            else
            {
                //Redirect to log in with error
                $data_to_view['try_edit_profile'] = '1';
                $this->load->view('log_in_form_view', $data_to_view);
            }
        }
    }

    /*Function to edit profile*/
    public function edit_profile()
    {
        if (!$this->has_session_timeout())
        {
            //Check if user log in properly
            if (isset($_SESSION["log_in_successfully"]) && isset($_SESSION["username"]) && isset($_SESSION["password"]))
            {
                if ($this->input->post('submit_edit_profile_form') != NULL)
                {
                    $result = $this->user_account_model->customer_edit_profile( $_SESSION['cus_id'],$this->input->post('modified_first_name'), $this->input->post('modified_last_name'),$this->input->post('modified_street_addr_shipping'),$this->input->post('modified_city_shipping'),$this->input->post('modified_state_shipping'),$this->input->post('modified_country_shipping'),$this->input->post('modified_dob'),$this->input->post('modified_credit_card'),$this->input->post('modified_security_code'),$this->input->post('modified_exp_month'),$this->input->post('modified_exp_year'),$this->input->post('modified_street_addr_billing'),$this->input->post('modified_city_billing'),$this->input->post('modified_state_billing'), $this->input->post('modified_country_billing'), $this->input->post('modified_phone'), $this->input->post('modified_email'), $this->input->post('modified_password'));
                    if ($result == 'false')
                    {
                        $data_to_view['customer_edit_profile'] = 'ERROR: retrieving info';
                    }
                    elseif ($result == 'true')
                    {
                        $data_to_view['customer_edit_profile'] = 'Successfully update your profile!';
                    }
                    else
                    {
                        $data_to_view['customer_edit_profile'] = $result;
                    }
                    $data_to_view['customer_info'] = $this->user_account_model->get_customer_info($_SESSION["cus_id"]);
                    $this->load->view('edit_profile_view', $data_to_view);
                }
            }
            else
            {
                //Redirect to log in with error
                $data_to_view['try_edit_profile'] = '1';
                $this->load->view('log_in_form_view', $data_to_view);
            }
        }
    }
}