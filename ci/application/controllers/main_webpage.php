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
        /*Establish session with username and password*/
        if(!isset($_SESSION))
        {
            session_start();
        }
    }

    /*Default function to display main webpage when customer connects to the site*/
    public function index()
    {
        $data_to_main_page_view['special_sale_display'] = $this->main_page_model->get_special_sale_display();
        $data_to_main_page_view['category_list'] = $this->main_page_model->get_category_dropDown_list();
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
                                array_push($_SESSION["shopping_cart"], array("qty" => $stored_cart_item["quantity"], "pid" => $stored_cart_item["product_id"]));
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
                            if ($isFirst == 0)
                            {
                                $_SESSION["shopping_cart"][] = array("qty" => $stored_cart_item["quantity"], "pid" => $stored_cart_item["product_id"]);
                                $isFirst += 1;
                            }
                            else
                            {
                                array_push($_SESSION["shopping_cart"], array("qty" => $stored_cart_item["quantity"], "pid" => $stored_cart_item["product_id"]));
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
        if (isset($S_SESSION['last_activity']) && isset($_SESSION['timeout']))
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
        return false;
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