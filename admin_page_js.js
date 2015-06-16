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
    if ((user_bd_yy < 1900) || (user_bd_mm < 1 || user_bd_mm > 12) || (user_bd_dd < 1 || user_bd_dd > 31) )
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

/*Function to validate input data on add employee page*/
function validate_add_employee_page()
{
    var fname_element = document.getElementById('fname');
    var lname_element = document.getElementById('lname');

    var myaddr = document.getElementById('myaddr');
    var mycity = document.getElementById('mycity');
    var mystate = document.getElementById('mystate');
    var mycountry = document.getElementById('mycountry');

    var mydob = document.getElementById('mydob');

    var mysalary = document.getElementById('mysalary');

    var marriage_status = document.getElementsByName('admin_add_radio1');
    var mygender = document.getElementsByName('admin_add_radio2');


    var myphone = document.getElementById('myphone');
    var myemail = document.getElementById('myemail');

    var user_name = document.getElementById('username');
    var pass_word = document.getElementById('pwd');

    var usertype = document.getElementsByName('admin_add_checkbox1[]');

    var myerror = document.getElementById('err_msg_add_employee');
    myerror.innerHTML = "";
    var isTrue = true;

    var marriage_status_check = validate_radio(marriage_status);
    var mygender_check = validate_radio(mygender);

    var usertype_check = validate_checkbox(usertype);

    var dob_check = validate_dob(mydob);

    if ((user_name.checkValidity() == false) || (pass_word.checkValidity() == false))
    {
        myerror.innerHTML += "Please enter an user name and/or password" + "<br/>";
        isTrue = false;
    }

    if (mysalary.checkValidity() == false)
    {
        myerror.innerHTML += "Please enter a correct salary" + "<br/>";
        isTrue = false;
    }

    if((fname_element.checkValidity() == false) || (lname_element.checkValidity() == false) || (mycity.checkValidity() == false) || (mystate.checkValidity() == false))
    {
        myerror.innerHTML += "First name/Last name/City/State can't contain number or State needs to be 2 characters" + "<br/>";
        isTrue = false;
    }

    if(myaddr.checkValidity() == false)
    {
        myerror.innerHTML += "Please enter a correct address" + "<br/>";
        isTrue = false;
    }

    if(mycountry.checkValidity() == false)
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
        myerror.innerHTML += "Please enter an correct email address" + "<br/>";
        isTrue = false;
    }

    /*Check radio button of radio1 - marriage status */
    if (marriage_status_check == false)
    {
        myerror.innerHTML += "Please select at least 1 radio button in marriage status section." + "<br/>";
        isTrue = false;
    }

    /*Check radio button of radio2 - gender */
    if (mygender_check == false)
    {
        myerror.innerHTML += "Please select at least 1 radio button in gender section." + "<br/>";
        isTrue = false;
    }

    /*Check checkbox of checkbox1 - Desert */
    if (usertype_check == false)
    {
        myerror.innerHTML += "Please select at least 1 checkbox in User type section." + "<br/>";
        isTrue = false;
    }

    /*Check date of birth*/
    if (dob_check == false)
    {
        myerror.innerHTML += "Please enter a correct date of birth" + "<br/>";
        isTrue = false;
    }

    return isTrue;
}