/**
 * Created by NguyenTran on 6/13/2015.
 */


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

/*Switch between forms and reports*/
function admin_transform( wherefrom, whereto )
{
    var d1 = document.getElementById('admin_page_form1');
    var d2 = document.getElementById('admin_page_add1');
    var d3 = document.getElementById('admin_page_add2_role');
    var d4 = document.getElementById('admin_page_add2_employee');

    if ((wherefrom == "admin_page_form1" && whereto == "admin_page_add1") || (wherefrom == "admin_page_add2_role" && whereto == "admin_page_add1") || (wherefrom == "admin_page_add2_employee" && whereto == "admin_page_add1"))
    {
        d1.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d2.style.display = "block";
    }

    else if((wherefrom == "admin_page_add1" && whereto == "admin_page_add2_role"))
    {
        d1.style.display = "none";
        d2.style.display = "none";
        d4.style.display = "none";
        d3.style.display = "block";
    }
    else if((wherefrom == "admin_page_add1" && whereto == "admin_page_add2_employee"))
    {
        d2.style.display = "none";
        d3.style.display = "none";
        d1.style.display = "none";
        d4.style.display = "block";
    }
    else if(wherefrom == "admin_page_add1" && whereto == "admin_page_form1" || (wherefrom == "admin_page_add2_role" && whereto == "admin_page_form1") || (wherefrom == "admin_page_add2_employee" && whereto == "admin_page_form1"))
    {
        d2.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d1.style.display = "block";
    }
}

/*Func to validate and transform page admin add1*/
function validate_add1_transform()
{
    var radio_element = document.getElementsByName('add1_radio1')
    var check_radio = validate_radio(radio_element);
    var errmsg = document.getElementById('err_msg');
    errmsg.innerHTML = '';
    if(check_radio)
    {
        var value_checked = value_radio(radio_element);
        if (value_checked == "newrole")
        {
            admin_transform('admin_page_add1','admin_page_add2_role');
        }
        else if(value_checked == "newemployee")
        {
            admin_transform('admin_page_add1','admin_page_add2_employee');
        }
    }
    else
    {
        errmsg.innerHTML += "Please select one option before continue" + "<br/>";
    }
}

/*Function to validate input data on add role page*/
function validate_add_role_page()
{
    isOk = true;
    var errmsg = document.getElementById('err_msg_add_role');
    errmsg.innerHTML = '';
    var usr_id = document.getElementById('usr_id_add_role');
    var radio_element = document.getElementsByName('role1_radio1');
    var check_radio = validate_radio(radio_element);
    if(usr_id.checkValidity() == false)
    {
        errmsg.innerHTML += "Please enter a numeric value for user id" + "<br/>";
        isOk = false;
    }
    if(!check_radio)
    {
        errmsg.innerHTML += "Please select a role to be added" + "<br/>";
        isOk = false;
    }
    return isOk;
}