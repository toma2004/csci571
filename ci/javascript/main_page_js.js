/**
 * Created by NguyenTran on 6/30/2015.
 */

/*jQuery ready function to be run when browser load the page*/
$(document).ready(initializePage);
var xmlhttp;
var last_destination='form1';
/*function to initialize the page*/
function initializePage()
{
    /*Create AJAX XMLHttpRequest object*/
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    check_size();

    /*What to do when button is clicked on sign up page*/
    $("#submit_sign_up_form").click(validate_sign_up_page);

    /*submit button on log-in page*/
    $("#submit_log_in").click(validate_log_in_page);

    /*Submit button on edit profile page*/
    $("#submit_edit_profile_form").click(validate_edit_profile_page);

    /*User tap on button icon in mobile mode*/
    $(".toggle-nav").click(toggleNavigation_helper);
}

function check_size()
{
    if ($(window).width()<= 1000)
    {
        $("#my_header").hide();
        $("#canvas").append("<div id='my_header'><a href='#' class='toggle-nav' id='bars'>☰</a><header><h1 id='main_header'>Welcome to N2 Food Catering</h1></header></div>");
    }
}


/*Function to validate sign up page*/
function validate_sign_up_page()
{
    var fname_element = $("#fname")[0]; /*return jQuery object. Need to get DOM object*/
    var lname_element = $('#lname')[0];

    var myaddr_shipping = $('#myaddr_shipping')[0];
    var mycity_shipping = $('#mycity_shipping')[0];
    var mystate_shipping = $('#mystate_shipping')[0];
    var mycountry_shipping = $('#mycountry_shipping')[0];

    var mydob = $('#mydob')[0];

    var mycredit_card = $('#mycreditcard')[0];
    var mysecurity_code = $('#mysecuritycode')[0];

    var exp_month = $('#myexpiredate_month')[0];
    var exp_year = $('#myexpiredate_year')[0];

    var myaddr_billing = $('#myaddr_billing')[0];
    var mycity_billing = $('#mycity_billing')[0];
    var mystate_billing = $('#mystate_billing')[0];
    var mycountry_billing = $('#mycountry_billing')[0];

    var myphone = $('#myphone')[0];
    var myemail = $('#myemail')[0];

    var user_name = $('#username')[0];
    var pass_word = $('#pwd')[0];

    var myerror = $('#error_msg_sign_up_form')[0];
    myerror.innerHTML = "";
    var isTrue = true;
    var data = "";
    var dob_check = validate_dob(mydob);

    if ((user_name.checkValidity() == false) || (pass_word.checkValidity() == false))
    {
        myerror.innerHTML += "Please enter an user name and/or password. Note that user name cannot contain any special characters" + "<br/>";
        isTrue = false;
    }
    else
    {
        /*Send AJAX request to server to check if a user name is used*/
        xmlhttp.onreadystatechange = isUnique;
        xmlhttp.open("POST","http://localhost/ci/index.php/main_webpage/user_sign_up",false);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        data = "sign_up_user_name="+user_name.value;
        xmlhttp.send(data);

        if (isUnique() == false)
        {
            myerror.innerHTML += "The user name you entered has been used. Please choose another one" + "<br/>";
            isTrue = false;
        }
    }

    if((fname_element.checkValidity() == false) || (lname_element.checkValidity() == false) || (mycity_shipping.checkValidity() == false) || (mystate_shipping.checkValidity() == false) || (mycity_billing.checkValidity() == false) || (mystate_billing.checkValidity() == false))
    {
        myerror.innerHTML += "First name/Last name/City/State can't contain number or State needs to be 2 characters" + "<br/>";
        isTrue = false;
    }

    if(myaddr_shipping.checkValidity() == false || myaddr_billing.checkValidity() == false)
    {
        myerror.innerHTML += "Please enter a correct address" + "<br/>";
        isTrue = false;
    }

    if(mycountry_shipping.checkValidity() == false || mycountry_billing.checkValidity() == false)
    {
        myerror.innerHTML += "Please enter a correct country name" + "<br/>";
        isTrue = false;
    }

    if(myphone.checkValidity() == false)
    {
        myerror.innerHTML += "Please enter a correct phone number" + "<br/>";
        isTrue = false;
    }

    if(myemail.checkValidity() == false)
    {
        myerror.innerHTML += "Please enter a correct email address" + "<br/>";
        isTrue = false;
    }
    else
    {
        /*Send AJAX request to server to check if an email  is used*/
        xmlhttp.onreadystatechange = isUnique;
        xmlhttp.open("POST","http://localhost/ci/index.php/main_webpage/user_sign_up",false);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        data = "sign_up_email="+myemail.value;
        xmlhttp.send(data);

        if (isUnique() == false)
        {
            myerror.innerHTML += "The email you entered has been used. Please choose another one" + "<br/>";
            isTrue = false;
        }
    }

    /*Check date of birth*/
    if (dob_check == false)
    {
        myerror.innerHTML += "Please enter a correct date of birth" + "<br/>";
        isTrue = false;
    }

    if (mycredit_card.checkValidity() == false)
    {
        myerror.innerHTML += "Please enter a correct credit card number" + "<br/>";
        isTrue = false;
    }

    if (mysecurity_code.checkValidity() == false)
    {
        myerror.innerHTML += "Please enter a correct security code number" + "<br/>";
        isTrue = false;
    }

    if (exp_month.checkValidity() == false || exp_month.value < 1 || exp_month.value > 12)
    {
        myerror.innerHTML += "Please enter a correct expiration month" + "<br/>";
        isTrue = false;
    }

    if (exp_year.checkValidity() == false || exp_year.value < 2015 || exp_year.value > 2030)
    {
        myerror.innerHTML += "Please enter a correct expiration year" + "<br/>";
        isTrue = false;
    }

    return isTrue;
}

/*Function to check date of birth*/
function validate_dob( mydob )
{
    /*Check if user has entered a date*/
    if (mydob.value == '')
    {
        return false;
    }

    var today = new Date();
    var birthday_str = (mydob.value.replace(/(\d{4})[\/-](\d{1,2})[\/-](\d{1,2})/, "$2/$3/$1")).split('/');

    var user_bd_yy = parseInt(birthday_str[2]);
    var user_bd_mm = parseInt(birthday_str[0]);
    var user_bd_dd = parseInt(birthday_str[1]);

    var user_birthday = new Date(mydob.value.replace(/(\d{4})[\/-](\d{1,2})[\/-](\d{1,2})/, "$2/$3/$1"));
    var y = today.getYear() - user_birthday.getYear();
    var m = today.getMonth() - user_birthday.getMonth();
    var d = today.getDate() - user_birthday.getDate();


    /*Check if user has entered valid values for date, month, year*/
    if ((user_bd_yy < 1900 || (today.getYear()-user_bd_yy > 0) ) || (user_bd_mm < 1 || user_bd_mm > 12) || (user_bd_dd < 1 || user_bd_dd > 31) )
    {
        return false;
    }
    /*Check to see if DOB is valid, no later than today date*/
    if(y < 0)
    {
        return false;
    }
    else if (y == 0)
    {
        if (m < 0)
        {
            return false;
        }
        else if (m == 0)
        {
            if (d < 0)
            {
                return false;
            }
        }
    }
    return true;
}


/*Function to receive reply from server to check if an element is unique in our database*/
function isUnique()
{
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
    {
        var response = xmlhttp.responseText;
        if (response == "false")
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}

/*function to validate log in page*/
function validate_log_in_page()
{
    var username = document.getElementById('usrname');
    var password = document.getElementById('pwd');
    var err_msg = document.getElementById('error_message_log_in_page');

    err_msg.innerHTML = '';
    if (username.checkValidity() == false || password.checkValidity() == false)
    {
        err_msg.innerHTML += "Please enter user name and/or password. Note that user name cannot contain any special characters <br/>";
        return false;
    }
    return true;
}

/*Function to validate edit profile page*/
function validate_edit_profile_page()
{
    var fname_element = $("#modified_first_name")[0]; /*return jQuery object. Need to get DOM object*/
    var lname_element = $('#modified_last_name')[0];
    var myaddr_shipping = $('#modified_street_addr_shipping')[0];
    var mycity_shipping = $('#modified_city_shipping')[0];
    var mystate_shipping = $('#modified_state_shipping')[0];
    var mycountry_shipping = $('#modified_country_shipping')[0];

    var mydob = $('#modified_dob')[0];

    var mycredit_card = $('#modified_credit_card')[0];
    var mysecurity_code = $('#modified_security_code')[0];

    var exp_month = $('#modified_exp_month')[0];
    var exp_year = $('#modified_exp_year')[0];

    var myaddr_billing = $('#modified_street_addr_billing')[0];
    var mycity_billing = $('#modified_city_billing')[0];
    var mystate_billing = $('#modified_state_billing')[0];
    var mycountry_billing = $('#modified_country_billing')[0];

    var myphone = $('#modified_phone')[0];
    var myemail = $('#modified_email')[0];

    var mypassword = $('#modified_password')[0];


    var myerror = $('#err_msg_edit_profile')[0];
    myerror.innerHTML = "";
    var isTrue = true;

    /*All blank case*/
    if (fname_element.value == '' && lname_element.value == '' && myaddr_shipping.value == '' && mycity_shipping.value == '' && mystate_shipping.value == '' && mycountry_shipping.value == '' && mydob.value == '' && mycredit_card.value == '' && mysecurity_code.value == '' && exp_month.value == '' && exp_year.value == '' && myaddr_billing.value == '' && mycity_billing.value == '' && mystate_billing.value == '' && mycountry_billing.value == '' && myphone.value == '' && myemail.value == '' && mypassword.value == '')
    {
        myerror.innerHTML += "You have not made any changes" + "<br/>";
        isTrue = false;
    }
    else
    {
        if((fname_element.value != '' && fname_element.checkValidity() == false) || (lname_element.value != '' && lname_element.checkValidity() == false) || (mycity_shipping.value != '' && mycity_shipping.checkValidity() == false) || (mystate_shipping.value != '' && mystate_shipping.checkValidity() == false) || (mycity_billing.value != '' && mycity_billing.checkValidity() == false) || (mystate_billing.value != '' && mystate_billing.checkValidity() == false))
        {
            myerror.innerHTML += "First name/Last name/City/State can't contain number or State needs to be 2 characters" + "<br/>";
            isTrue = false;
        }

        if((myaddr_shipping.value != '' && myaddr_shipping.checkValidity() == false) || (myaddr_billing.value != '' && myaddr_billing.checkValidity() == false))
        {
            myerror.innerHTML += "Please enter a correct address" + "<br/>";
            isTrue = false;
        }

        if((mycountry_shipping.value != '' && mycountry_shipping.checkValidity() == false) || (mycountry_billing.value != '' && mycountry_billing.checkValidity() == false))
        {
            myerror.innerHTML += "Please enter a correct country name" + "<br/>";
            isTrue = false;
        }

        if(myphone.value != '' && myphone.checkValidity() == false)
        {
            myerror.innerHTML += "Please enter a correct phone number" + "<br/>";
            isTrue = false;
        }

        if (myemail.value != '')
        {
            if(myemail.checkValidity() == false)
            {
                myerror.innerHTML += "Please enter a correct email address" + "<br/>";
                isTrue = false;
            }
            else
            {
                /*Send AJAX request to server to check if an email  is used*/
                xmlhttp.onreadystatechange = isUnique;
                xmlhttp.open("POST","http://localhost/ci/index.php/main_webpage/user_sign_up",false);
                xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                var data = "sign_up_email="+myemail.value;
                xmlhttp.send(data);

                if (isUnique() == false)
                {
                    myerror.innerHTML += "The email you entered has been used. Please choose another one" + "<br/>";
                    isTrue = false;
                }
            }
        }
        /*Check date of birth*/
        if (mydob.value != '')
        {
            if(validate_dob(mydob) == false)
            {
                myerror.innerHTML += "Please enter a correct date of birth" + "<br/>";
                isTrue = false;
            }
        }

        if (mycredit_card.value != '' && mycredit_card.checkValidity() == false)
        {
            myerror.innerHTML += "Please enter a correct credit card number" + "<br/>";
            isTrue = false;
        }

        if (mysecurity_code.value != '' && mysecurity_code.checkValidity() == false)
        {
            myerror.innerHTML += "Please enter a correct security code number" + "<br/>";
            isTrue = false;
        }

        if (exp_month.value != '' && (exp_month.checkValidity() == false || exp_month.value < 1 || exp_month.value > 12))
        {
            myerror.innerHTML += "Please enter a correct expiration month" + "<br/>";
            isTrue = false;
        }

        if (exp_year.value != '' && (exp_year.checkValidity() == false || exp_year.value < 2015 || exp_year.value > 2030))
        {
            myerror.innerHTML += "Please enter a correct expiration year" + "<br/>";
            isTrue = false;
        }
    }
    return isTrue;
}

/*Function to display category*/
function display_category ( my_category_id )
{
    xmlhttp.onreadystatechange = display_category_result;
    xmlhttp.open("POST","http://localhost/ci/index.php/main_webpage/display_products_in_category",true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    var data = '';
    data += "display_category_id="+my_category_id;
    xmlhttp.send(data);
}

/*Function to handle response from server to display category*/
function display_category_result()
{
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
    {
        if (xmlhttp.responseText == 'time_out')
        {
            window.location.replace("http://localhost/ci/html/log_in_page_time_out.html");
        }
        else
        {
            document.getElementById('display_category_form').innerHTML = xmlhttp.responseText;
            div_transform("display_category_div");
        }
    }
}
/*function toggle navigation helper*/
function toggleNavigation_helper()
{
    toggleNavigation('none');
}
/*Function to toggle navigation side bar*/
function toggleNavigation( destination )
{
    var div_name;
    if ($('#container').hasClass('display-nav'))
    {
        // Close Nav
        $('#container').removeClass('display-nav');
        //$("#form1").show();
        if (destination == 'none')
        {
            div_name = "#"+last_destination;
            $(div_name).show();
        }
        else
        {
            div_name = "#"+destination;
            $(div_name).show();
        }
    }
    else
    {
        // Open Nav
        if (destination != 'none')
        {
            div_name = "#"+destination;
            $(div_name).hide();
            last_destination = destination;
        }
        else
        {
            if ($("#form1").is(':visible'))
            {
                $("#form1").hide();
                last_destination = "form1";
            }
            else if ($("#display_category_div").is(':visible'))
            {
                $("#display_category_div").hide();
                last_destination = "display_category_div";
            }
            else if ($("#edit_shopping_cart_div").is(':visible'))
            {
                $("#edit_shopping_cart_div").hide();
                last_destination = "edit_shopping_cart_div";
            }
            else if ($("#checkout_summary_div").is(':visible'))
            {
                $("#checkout_summary_div").hide();
                last_destination = "checkout_summary_div";
            }
            else if ($("#past_orders_summary_div").is(':visible'))
            {
                $("#past_orders_summary_div").hide();
                last_destination = "past_orders_summary_div";
            }
            else if ($("#past_order_detail_div").is(':visible'))
            {
                $("#past_order_detail_div").hide();
                last_destination = "past_order_detail_div";
            }
            else if ($("#contact_us_div").is(':visible'))
            {
                $("#contact_us_div").hide();
                last_destination = "contact_us_div";
            }
        }
        $('#container').addClass('display-nav');
    }
}
/*Function to navigate between div*/
function div_transform( destination )
{
    var d1 = document.getElementById('form1');
    var d2 = document.getElementById('display_category_div');
    var d3 = document.getElementById('edit_shopping_cart_div');
    var d4 = document.getElementById('checkout_summary_div');
    var d5 = document.getElementById('past_orders_summary_div');
    var d6 = document.getElementById('past_order_detail_div');
    var d7 = document.getElementById('contact_us_div');
    toggleNavigation( destination );
    if (destination == "form1")
    {
        d2.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d5.style.display = "none";
        d6.style.display = "none";
        d7.style.display = "none";
        d1.style.display = "block";
    }
    else if (destination == "display_category_div")
    {
        d1.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d5.style.display = "none";
        d6.style.display = "none";
        d7.style.display = "none";
        d2.style.display = "block";
    }
    else if (destination == "edit_shopping_cart_div")
    {
        d1.style.display = "none";
        d2.style.display = "none";
        d4.style.display = "none";
        d5.style.display = "none";
        d6.style.display = "none";
        d7.style.display = "none";
        d3.style.display = "block";
    }
    else if (destination == "checkout_summary_div")
    {
        d1.style.display = "none";
        d2.style.display = "none";
        d3.style.display = "none";
        d5.style.display = "none";
        d6.style.display = "none";
        d7.style.display = "none";
        d4.style.display = "block";
    }
    else if (destination == "past_orders_summary_div")
    {
        d1.style.display = "none";
        d2.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d6.style.display = "none";
        d7.style.display = "none";
        d5.style.display = "block";
    }
    else if (destination == "past_order_detail_div")
    {
        d1.style.display = "none";
        d2.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d5.style.display = "none";
        d7.style.display = "none";
        d6.style.display = "block";
    }
    else if (destination == "contact_us_div")
    {
        d1.style.display = "none";
        d2.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d5.style.display = "none";
        d6.style.display = "none";
        d7.style.display = "block";
    }
}

/*Function to request shopping cart info from server so that customers can see and edit*/
function request_shopping_cart_info()
{
    /*Send AJAX request to server for shopping card info*/
    xmlhttp.onreadystatechange = display_shopping_cart;
    xmlhttp.open("POST","http://localhost/ci/index.php/main_webpage/display_shopping_cart",true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send("display_shopping_cart=1");
}

/*Function to handle reply from server to display shopping cart for edit*/
function display_shopping_cart()
{
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
    {
        document.getElementById('edit_shopping_cart_form').innerHTML = xmlhttp.responseText;
        div_transform("edit_shopping_cart_div");
    }
}

/*Function to send request to server to change*/
function change_quality( product_id, quantity_val)
{
    quantity_val += 1; /*This is needed since the parameter is an index*/
    /*Send AJAX request to server to request quantity change of a product in shopping cart*/
    xmlhttp.onreadystatechange = result_request_quantity_change;
    xmlhttp.open("POST","http://localhost/ci/index.php/main_webpage/change_quantity_product_cart",true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    var data = '';
    data += 'change_shopping_cart_product_id='+product_id+'&quantity='+quantity_val;
    xmlhttp.send(data);
}

/*Check result from server to see if we have successfully update quantity of a product in our cart*/
function result_request_quantity_change()
{
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
    {
        if (xmlhttp.responseText == 'fail')
        {
            document.getElementById('edit_shopping_cart_form').innerHTML = '<p style="color: red;">Failed to update shopping cart quantity</p>';
        }
    }
}

/*function to delete a product from shopping cart*/
function delete_product_cart( product_id )
{
    /*Send AJAX request to server to remove an item from shopping cart*/
    xmlhttp.onreadystatechange = result_request_remove_item;
    xmlhttp.open("POST","http://localhost/ci/index.php/main_webpage/delete_product_cart",true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    var data = '';
    data += 'remove_item_product_id='+product_id;
    xmlhttp.send(data);
}

/*Check result from server to see if we have successfully delete a product from our cart*/
function result_request_remove_item()
{
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
    {
        if (xmlhttp.responseText == 'fail')
        {
            document.getElementById('edit_shopping_cart_form').innerHTML = '<p style="color: red;">Failed to remove product from shopping cart</p>';
        }
        else
        {
            /*success. Reload the shopping cart page for customer to see the change*/
            request_shopping_cart_info();
        }
    }
}

/*Function to remove entire shopping cart*/
function delete_entire_cart()
{
    /*Send AJAX request to server to remove entire shopping cart*/
    xmlhttp.onreadystatechange = result_request_remove_entire_cart;
    xmlhttp.open("POST","http://localhost/ci/index.php/main_webpage/remove_entire_shopping_cart",true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send("remove_entire_cart=1");
}

/*Function to check result from server if we have successfully remove entire cart*/
function result_request_remove_entire_cart()
{
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
    {
        if (xmlhttp.responseText == 'fail')
        {
            document.getElementById('edit_shopping_cart_form').innerHTML = '<p style="color: red;">Failed to remove entire shopping cart</p>';
        }
        else
        {
            document.getElementById('edit_shopping_cart_form').innerHTML = '<p style="color: red; font-weight: bold;">Your shopping cart is empty</p>';
        }
    }
}

/*Function to send request to server that customer wants to checkout*/
function request_check_out()
{
    /*Send AJAX request to server to request checkout*/
    xmlhttp.onreadystatechange = result_request_check_out;
    xmlhttp.open("POST","http://localhost/ci/index.php/main_webpage/check_out_cart",true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send("request_for_checkout=1");
}

/*Function to receive reply from server and display checkout page*/
function result_request_check_out()
{
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
    {
        if (xmlhttp.responseText == 'time_out')
        {
            window.location.replace("http://localhost/ci/html/log_in_page_time_out.html");
        }
        else
        {
            document.getElementById('checkout_summary_form').innerHTML = xmlhttp.responseText;
            div_transform("checkout_summary_div");
        }
    }
}

/*Function to request to server that customer wants to see all past orders*/
function request_past_orders_info()
{
    /*Send AJAX request to server to request past orders*/
    xmlhttp.onreadystatechange = result_request_past_order;
    xmlhttp.open("POST","http://localhost/ci/index.php/main_webpage/display_past_order",true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send("request_for_past_order=1");
}

/*Function to receive a reply from server and display past order page*/
function result_request_past_order()
{
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
    {
        if (xmlhttp.responseText == 'time_out')
        {
            window.location.replace("http://localhost/ci/html/log_in_page_time_out.html");
        }
        else
        {
            document.getElementById('past_orders_summary_form').innerHTML = xmlhttp.responseText;
            div_transform("past_orders_summary_div");
        }
    }
}

/*Function to request to server detail past order*/
function request_detail_past_order( order_id )
{
    /*Send AJAX request to server to request past order detail based on order id*/
    xmlhttp.onreadystatechange = result_request_past_order_detail;
    xmlhttp.open("POST","http://localhost/ci/index.php/main_webpage/display_detail_past_order",true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    var data = "";
    data += "request_past_order_detail="+order_id;
    xmlhttp.send(data);
}

/*Function to receive reply from server and display detail of a past order*/
function result_request_past_order_detail()
{
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
    {
        if (xmlhttp.responseText == 'time_out')
        {
            window.location.replace("http://localhost/ci/html/log_in_page_time_out.html");
        }
        else
        {
            document.getElementById('past_order_detail_form').innerHTML = xmlhttp.responseText;
            div_transform("past_order_detail_div");
        }
    }
}

/*Function to display contact us info*/
function request_contact()
{
    document.getElementById('contact_us_form').innerHTML = '<p>Email: trannk@usc.edu</p><p>Phone number: (123) 123-4567</p>';
    div_transform('contact_us_div');
}