/**
 * Created by NguyenTran on 6/21/2015.
 */

/*jQuery ready function to be run when browser load the page*/
$(document).ready(checkAll);


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
    // $("#manager_homepage_product_search_clicked").click(transform_managerHomePage_productSearchPage);
    $("#manager_homepage_employee_search_clicked").click(transform_managerHomePage_employeeSearchPage);
    // $("#manager_homepage_special_sale_search_clicked").click(transform_managerHomePage_specialsaleSearchPage);
    // $("#manager_homepage_logout").click(manager_homepage_logout);

    /*All events in employee search homepage*/
    $("#back_homepage_from_employee_search").click(back_homepage);
    $("#submit_employee_search").click(send_employee_search_data_to_server);
}

/*Function to handle transform from manager homepage to employee search page*/
function transform_managerHomePage_employeeSearchPage()
{
    manager_transform('manager_homepage','manager_page_employee_search');
}

function back_homepage()
{
    manager_transform('not matter','manager_homepage');
}

/*Function to transform between div*/
function manager_transform( wherefrom, whereto )
{
    var d1 = document.getElementById('manager_homepage');
    var d2 = document.getElementById('manager_page_employee_search');
    if (wherefrom == 'manager_homepage' && whereto == "manager_page_employee_search")
    {
        d1.style.display = "none";
        d2.style.display = "block";
    }
    else if(whereto == "manager_homepage")
    {
        d2.style.display = "none";
        d1.style.display = "block";
    }
}

/*Function to validate form data in employee search*/
function validate_employee_search()
{
    var pay_range_low = document.getElementById('employee_pay_range_low');
    var pay_range_high = document.getElementById('employee_pay_range_high');

    var user_type_cb = document.getElementsByName('manager_employee_checkbox1');
    var user_type_cb_check = validate_checkbox(user_type_cb);

    var err_msg = document.getElementById('err_msg_employee_search');
    err_msg.innerHTML = '';
    var isTrue = true;

    if(pay_range_high.value == '' && pay_range_low.value =='' && user_type_cb_check == false)
    {
        err_msg.innerHTML += "Please make a least 1 search criteria<br/>";
        isTrue = false;
    }

    if(pay_range_high.value != '')
    {
        if (pay_range_low.value == '')
        {
            err_msg.innerHTML += "Please select the lower pay range<br/>";
            isTrue = false;
        }
        else
        {
            if(pay_range_high.checkValidity() == false)
            {
                err_msg.innerHTML += "Higher pay range is not in the right format. Please enter numbers only and in range between 0-1000000 (inclusive)<br/>";
                isTrue = false;
            }
            else if(pay_range_high.value < pay_range_low.value)
            {
                err_msg.innerHTML += "Higher pay range is less than lower pay range<br/>";
                isTrue = false;
            }
        }
    }

    if(pay_range_low.value != '')
    {
        if (pay_range_high.value == '')
        {
            err_msg.innerHTML += "Please select the higher pay range<br/>";
            isTrue = false;
        }
        else
        {
            if(pay_range_low.checkValidity() == false)
            {
                err_msg.innerHTML += "Lower pay range is not in the right format. Please enter numbers only and in range between 0-1000000 (inclusive)<br/>";
                isTrue = false;
            }
            else if(pay_range_low.value > pay_range_high.value)
            {
                err_msg.innerHTML += "Higher pay range is less than lower pay range<br/>";
                isTrue = false;
            }
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

        var user_type_cb = document.getElementsByName('manager_employee_checkbox1');
        var user_type_cb_check = validate_checkbox(user_type_cb);
        var name_val_pay = "";
        var name_user_type = "";
        var data_send = "";

        /*Create AJAX XMLHttpRequest object*/
        var xmlhttp;
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

        /*See what data to send to server*/
        if (pay_range_high.value != '' && pay_range_low.value != '')
        {
            name_val_pay += "employee_pay_range_low="+pay_range_low.value+"&employee_pay_range_high="+pay_range_high.value;
        }

        if(!user_type_cb_check)
        {
            var arr_user_type = value_checkboxes(user_type_cb);
            var str_user_type = arr_user_type.join();
            name_user_type += "manager_employee_user_type="+str_user_type;
        }

        if(name_val_pay != "" && name_user_type != "")
        {
            /*Concatenate and send both info to server*/
            data_send += name_val_pay+"&"+name_user_type;
            xmlhttp.send(data_send);
        }
        else if(name_val_pay == '')
        {
            xmlhttp.send(name_user_type);
        }
        else
        {
            xmlhttp.send(name_val_pay);
        }
    }
}

/*Function to display employee search result*/
function display_result_employee_search()
{

}
