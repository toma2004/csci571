/**
 * Created by NguyenTran on 6/21/2015.
 */

/*jQuery ready function to be run when browser load the page*/
$(document).ready(checkAll);
var xmlhttp;

/*Function to validate radio button to at least 1 checked */
function validate_radio( radio_element )
{
    var isOk = false;
    for (var i=0; i < radio_element.length; i++)
    {
        if (radio_element[i].checked)
        {
            isOk = true;
            break;
        }
    }
    return isOk;
}

/*Function to obtain value of checked radio button
 * Only 1 value is allowed to be chosen*/
function value_radio( radio_element)
{
    var initial = "none";
    for (var i = 0; i < radio_element.length; i++)
    {
        if (radio_element[i].checked)
        {
            initial = radio_element[i].value;
            break;
        }
    }
    return initial;
}

/*Function to validate checkbox to at least 1 checked */
function validate_checkbox( checkbox_element )
{
    var isOk = false;
    for (var i=0; i < checkbox_element.length; i++)
    {
        if (checkbox_element[i].checked)
        {
            isOk = true;
            break;
        }
    }
    return isOk;
}

/*Function to obtain values of checkboxes
 * multiple values are allowed to be checked*/
function value_checkboxes( checkbox_element )
{
    var initial = [];
    for (var i = 0; i < checkbox_element.length; i++)
    {
        if (checkbox_element[i].checked)
        {
            initial.push(checkbox_element[i].value);
        }
    }
    return initial;
}

/*Function to check if start date is before end date*/
function check_date_before (date1,date2)
{
    /*Check if user has enter a value for either of the date*/
    if (date1.value == '' || date2.value == '')
    {
        return false;
    }
    var today = new Date();

    var date1_arr = (date1.value.replace(/(\d{4})[\/-](\d{1,2})[\/-](\d{1,2})/, "$2/$3/$1")).split('/');
    var date2_arr = (date2.value.replace(/(\d{4})[\/-](\d{1,2})[\/-](\d{1,2})/, "$2/$3/$1")).split('/');

    var date1_yy = parseInt(date1_arr[2]);
    var date1_mm = parseInt(date1_arr[0]);
    var date1_dd = parseInt(date1_arr[1]);

    var date2_yy = parseInt(date2_arr[2]);
    var date2_mm = parseInt(date2_arr[0]);
    var date2_dd = parseInt(date2_arr[1]);

    /*Check if user has entered valid values for date, month, year*/
    if ((date1_yy < 1900 || (today.getYear() - date1_yy > 5)) || (date1_mm < 1 || date1_mm > 12) || (date1_dd < 1 || date1_dd > 31) )
    {
        return false;
    }

    /*Check if start date is before end date*/
    if (date1_yy - date2_yy > 0) /*start date year is more than end date year*/
    {
        return false;
    }
    else if(date1_yy - date2_yy == 0)
    {
        if(date1_mm - date2_mm > 0) /*start date month is more than end date month*/
        {
            return false;
        }
        else if(date1_mm - date2_mm == 0)
        {
            if (date1_dd - date2_dd > 0) /*start date is more than end date*/
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

/*Function to check all event in our HTML using jQuery*/
function checkAll()
{
    /*All events in manager homepage*/
    $("#manager_homepage_product_search_clicked").click(transform_managerHomePage_productSearchPage);
    $("#manager_homepage_employee_search_clicked").click(transform_managerHomePage_employeeSearchPage);
    $("#manager_homepage_special_sale_search_clicked").click(transform_managerHomePage_specialsaleSearchPage);
    $("#manager_homepage_order_search_clicked").click(transform_managerHomePage_orderSearchPage);


    /*All events in employee search homepage*/
    $("#back_homepage_from_employee_search").click(back_homepage);
    $("#submit_employee_search").click(send_employee_search_data_to_server);

    /*All events in product search homepage*/
    $("#back_homepage_from_product_search").click(back_homepage);
    $("#submit_product_search").click(send_product_search_data_to_server);

    /*All events in special sale search homepage*/
    $("#back_homepage_from_special_sale_search").click(back_homepage);
    $("#submit_special_sale_search").click(send_special_sale_search_data_to_server);

    /*All events in order search homepage*/
    $("#back_homepage_from_order_search").click(back_homepage);
    //$("#submit_order_search").click(send_order_search_data_to_server);
}

/*Function to handle transform from manager homepage to employee search page*/
function transform_managerHomePage_employeeSearchPage()
{
    manager_transform('manager_homepage','manager_page_employee_search');
}

/*Function to handle transform from manager homepage to product search page*/
function transform_managerHomePage_productSearchPage()
{
    manager_transform('manager_homepage','manager_page_product_search');
}

/*Function to handle transform from manager homepage to special sale search page*/
function transform_managerHomePage_specialsaleSearchPage()
{
    manager_transform('manager_homepage','manager_page_special_sale_search');
}

/*Function to handle transform from manager homepage to order search page*/
function transform_managerHomePage_orderSearchPage()
{
    manager_transform('manager_homepage','manager_page_order_search');
}
/*Back to manager home page function*/
function back_homepage()
{
    manager_transform('not matter','manager_homepage');
}

/*Function to transform between div*/
function manager_transform( wherefrom, whereto )
{
    var d1 = document.getElementById('manager_homepage');
    var d2 = document.getElementById('manager_page_employee_search');
    var d3 = document.getElementById('manager_page_product_search');
    var d4 = document.getElementById('manager_page_special_sale_search');
    var d5 = document.getElementById('manager_page_order_search');

    if (wherefrom == 'manager_homepage' && whereto == "manager_page_employee_search")
    {
        d1.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d5.style.display = "none";
        d2.style.display = "block";
    }
    else if(wherefrom == "manager_homepage" && whereto == "manager_page_product_search")
    {
        d1.style.display = "none";
        d2.style.display = "none";
        d4.style.display = "none";
        d5.style.display = "none";
        d3.style.display = "block";
    }
    else if(wherefrom == "manager_homepage" && whereto == "manager_page_special_sale_search")
    {
        d1.style.display = "none";
        d2.style.display = "none";
        d3.style.display = "none";
        d5.style.display = "none";
        d4.style.display = "block";
    }
    else if(whereto == "manager_homepage")
    {
        d2.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d5.style.display = "none";
        d1.style.display = "block";
    }
    else if (whereto == "manager_page_order_search")
    {
        d2.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d1.style.display = "none";
        d5.style.display = "block";
    }
}

/*Function to validate form data in employee search*/
function validate_employee_search()
{
    var pay_range_low = document.getElementById('employee_pay_range_low');
    var pay_range_high = document.getElementById('employee_pay_range_high');

    var user_type_cb = document.getElementsByName('manager_employee_checkbox1[]');
    var user_type_cb_check = validate_checkbox(user_type_cb);

    var err_msg = document.getElementById('err_msg_employee_search');
    err_msg.innerHTML = '';
    var isTrue = true;

    var int_pay_low = parseInt(pay_range_low.value);
    var int_pay_high = parseInt(pay_range_high.value);

    if(pay_range_high.checkValidity() == false)
    {
        err_msg.innerHTML += "Higher pay range is not in the right format. Please enter numbers only and in range between 0-1000000 (inclusive). If there is a decimal point, please only 2 digits after the decimal point<br/>";
        isTrue = false;
    }


    if(pay_range_low.checkValidity() == false)
    {
        err_msg.innerHTML += "Lower pay range is not in the right format. Please enter numbers only and in range between 0-1000000 (inclusive). If there is a decimal point, please only 2 digits after the decimal point<br/>";
        isTrue = false;
    }
    if (!isTrue)
    {
        return false;
    }


    if(pay_range_high.value == '' && pay_range_low.value == '' && !user_type_cb_check)
    {
        err_msg.innerHTML += "Please make a least 1 search criteria<br/>";
        return false;
    }


    if(pay_range_high.value == '' && pay_range_low.value == '' && user_type_cb_check)
    {
        isTrue = true;
    }
    else
    {
        if(pay_range_high.value != '' && pay_range_low.value != '')
        {
            if(int_pay_high < int_pay_low)
            {
                err_msg.innerHTML += "Higher pay range is less than lower pay range<br/>";
                isTrue = false;
            }
        }
        else
        {
            err_msg.innerHTML += "Please select the lower/higher pay range<br/>";
            isTrue = false;
        }
    }
    return isTrue;
}

/*Function to send employee search data to web server*/
function send_employee_search_data_to_server()
{
    if(validate_employee_search())
    {
        var pay_range_low = document.getElementById('employee_pay_range_low');
        var pay_range_high = document.getElementById('employee_pay_range_high');

        var user_type_cb = document.getElementsByName('manager_employee_checkbox1[]');

        var data_send = "";

        var arr_user_type = value_checkboxes(user_type_cb);
        var str_user_type = arr_user_type.join();

        /*Create AJAX XMLHttpRequest object*/
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = display_result_employee_search;
        xmlhttp.open("POST","manager_page.php",true);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");

        /*Send data*/
        data_send += "employee_pay_range_low="+pay_range_low.value+"&employee_pay_range_high="+pay_range_high.value+"&manager_employee_user_type="+str_user_type;
        xmlhttp.send(data_send);

    }
}

/*Function to display employee search result*/
function display_result_employee_search()
{
    /*Check status of responded data */
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
    {
        document.getElementById('manager_page_employee_search_display_search_result').innerHTML = xmlhttp.responseText;
    }
}

/*Function to validate form data in product search*/
function validate_product_search()
{
    var price_range_low = document.getElementById('product_price_range_low');
    var price_range_high = document.getElementById('product_price_range_high');

    var product_name = document.getElementById('product_search_name');
    var product_category = document.getElementById('product_search_category');

    var err_msg = document.getElementById('err_msg_product_search');
    err_msg.innerHTML = '';
    var isTrue = true;

    var int_price_low = parseFloat(price_range_low.value);
    var int_price_high = parseFloat(price_range_high.value);

    if(price_range_high.checkValidity() == false)
    {
        err_msg.innerHTML += "Higher price range is not in the right format. Please enter numbers only and in range between 0-9999 (inclusive). If there is a decimal point, please only 2 digits after the decimal point<br/>";
        isTrue = false;
    }

    if(price_range_low.checkValidity() == false)
    {
        err_msg.innerHTML += "Lower price range is not in the right format. Please enter numbers only and in range between 0-9999 (inclusive). If there is a decimal point, please only 2 digits after the decimal point<br/>";
        isTrue = false;
    }
    if(product_name.checkValidity() == false)
    {
        err_msg.innerHTML += "Please enter a correct product name (no special characters)<br/>";
        isTrue = false;
    }
    if(product_category.checkValidity() == false)
    {
        err_msg.innerHTML += "Please enter a correct product category (no special characters)<br/>";
        isTrue = false;
    }
    if (!isTrue)
    {
        return false;
    }


    if(price_range_high.value == '' && price_range_low.value == '' && product_category.value == '' && product_name.value == '')
    {
        err_msg.innerHTML += "Please make a least 1 search criteria<br/>";
        return false;
    }


    if(price_range_high.value == '' && price_range_low.value == '' && (product_category.value != '' || product_name.value != ''))
    {
        isTrue = true;
    }
    else
    {
        if(price_range_high.value != '' && price_range_low.value != '')
        {
            if(int_price_high < int_price_low)
            {
                err_msg.innerHTML += "ERROR: Higher price range is less than lower price range<br/>";
                isTrue = false;
            }
        }
        else
        {
            err_msg.innerHTML += "Please select the lower/higher price range<br/>";
            isTrue = false;
        }
    }
    return isTrue;
}

/*Function to send product search data to web server*/
function send_product_search_data_to_server()
{
    if(validate_product_search())
    {
        var price_range_low = document.getElementById('product_price_range_low');
        var price_range_high = document.getElementById('product_price_range_high');

        var product_name = document.getElementById('product_search_name');
        var product_category = document.getElementById('product_search_category');

        var data_send = "";


        /*Create AJAX XMLHttpRequest object*/
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = display_result_product_search;
        xmlhttp.open("POST","manager_page.php",true);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");

        /*Send data*/
        data_send += "product_price_range_low="+price_range_low.value+"&product_price_range_high="+price_range_high.value+"&product_search_name="+product_name.value+"&product_search_category="+product_category.value;
        xmlhttp.send(data_send);
    }
}

/*Function to display employee search result*/
function display_result_product_search()
{
    /*Check status of responded data */
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
    {
        document.getElementById('manager_page_product_search_display_search_result').innerHTML = xmlhttp.responseText;
    }
}

/*Function to validate form data in special sale search*/
function validate_special_sale_search()
{
    var price_range_low = document.getElementById('product_price_range_low_special_sale');
    var price_range_high = document.getElementById('product_price_range_high_special_sale');

    var product_name = document.getElementById('special_sale_search_product_name');
    var product_category = document.getElementById('special_sale_search_product_category');

    var start_date = document.getElementById('special_sale_mystart_date');
    var end_date = document.getElementById('special_sale_myend_date');

    var err_msg = document.getElementById('err_msg_special_sale_search');
    err_msg.innerHTML = '';
    var isTrue = true;

    var int_price_low = parseFloat(price_range_low.value);
    var int_price_high = parseFloat(price_range_high.value);


    if(price_range_high.checkValidity() == false)
    {
        err_msg.innerHTML += "Higher price range is not in the right format. Please enter numbers only and in range between 0-9999 (inclusive). If there is a decimal point, please only 2 digits after the decimal point<br/>";
        isTrue = false;
    }

    if(price_range_low.checkValidity() == false)
    {
        err_msg.innerHTML += "Lower price range is not in the right format. Please enter numbers only and in range between 0-9999 (inclusive). If there is a decimal point, please only 2 digits after the decimal point<br/>";
        isTrue = false;
    }
    if(product_name.checkValidity() == false)
    {
        err_msg.innerHTML += "Please enter a correct product name (no special characters)<br/>";
        isTrue = false;
    }
    if(product_category.checkValidity() == false)
    {
        err_msg.innerHTML += "Please enter a correct product category (no special characters)<br/>";
        isTrue = false;
    }
    if (!isTrue)
    {
        return false;
    }


    if(price_range_high.value == '' && price_range_low.value == '' && product_category.value == '' && product_name.value == '' && start_date.value == '' && end_date.value == '')
    {
        err_msg.innerHTML += "Please make a least 1 search criteria<br/>";
        return false;
    }


    if(price_range_high.value == '' && price_range_low.value == '' && start_date.value == '' && end_date.value == '' && (product_category.value != '' || product_name.value != ''))
    {
        isTrue = true;
    }
    else if (start_date.value != '' || end_date.value != '')
    {
        /*validate dates*/
        if (!check_date_before(start_date,end_date)) /*start date is after end date*/
        {
            err_msg.innerHTML += "Either start date can't be after end date or one of the dates you entered is invalid. Please double check your values<br/>";
            return false;
        }
    }
    else
    {
        if(price_range_high.value != '' && price_range_low.value != '')
        {
            if(int_price_high < int_price_low)
            {
                err_msg.innerHTML += "ERROR: Higher price range is less than lower price range<br/>";
                isTrue = false;
            }
        }
        else
        {
            err_msg.innerHTML += "Please select the lower/higher price range<br/>";
            isTrue = false;
        }
    }
    return isTrue;
}

/*Function to send special sale search request to server*/
function send_special_sale_search_data_to_server()
{
    if(validate_special_sale_search())
    {
        var price_range_low = document.getElementById('product_price_range_low_special_sale');
        var price_range_high = document.getElementById('product_price_range_high_special_sale');

        var product_name = document.getElementById('special_sale_search_product_name');
        var product_category = document.getElementById('special_sale_search_product_category');

        var start_date = document.getElementById('special_sale_mystart_date');
        var end_date = document.getElementById('special_sale_myend_date');

        var data_send = "";


        /*Create AJAX XMLHttpRequest object*/
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = display_result_special_sale_search;
        xmlhttp.open("POST","manager_page.php",true);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");

        /*Send data*/
        data_send += "product_price_range_low_special_sale="+price_range_low.value+"&product_price_range_high_special_sale="+price_range_high.value+"&special_sale_search_product_name="+product_name.value+"&special_sale_search_product_category="+product_category.value+"&special_sale_start_date="+start_date.value+"&special_sale_end_date="+end_date.value;
        xmlhttp.send(data_send);
    }
}

/*Function to display employee search result*/
function display_result_special_sale_search()
{
    /*Check status of responded data */
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
    {
        document.getElementById('manager_page_special_sale_search_display_search_result').innerHTML = xmlhttp.responseText;
    }
}

/*Function to validate order search page*/
function validate_order_search()
{
    var start_date = document.getElementById('start_date_order');
    var end_date = document.getElementById('end_date_order');

    var err_msg = document.getElementById('err_msg_order_search');
    err_msg.innerHTML = '';
    var isTrue = true;

    if (start_date.value != '' && end_date.value != '')
    {
        /*validate dates*/
        if (!check_date_before(start_date,end_date)) /*start date is after end date*/
        {
            err_msg.innerHTML += "Either start date can't be after end date or one of the dates you entered is invalid. Please double check your values<br/>";
            return false;
        }
    }
    else if ((start_date.value == '' && end_date.value != '') || (start_date.value != '' && end_date.value == ''))
    {
        err_msg.innerHTML += "Either start date or end date is missing. Please double check your values<br/>";
        return false;
    }
    return isTrue;
}