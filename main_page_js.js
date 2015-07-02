/**
 * Created by NguyenTran on 6/30/2015.
 */

/*jQuery ready function to be run when browser load the page*/
$(document).ready(initializePage);
var xmlhttp;

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
    xmlhttp.onreadystatechange = display_result_special_sale_main_page;
    xmlhttp.open("POST","main_page.php",true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send("special_sale_display=1");

    /*What to do when button is clicked*/
    $("#submit_sign_up_form").click(validate_sign_up_page);
}

/*function to receive response from server and display special sale on main page*/
function display_result_special_sale_main_page()
{
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
    {
        document.getElementById('main_page_form').innerHTML = xmlhttp.responseText;
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
        xmlhttp.open("POST","main_page.php",false);
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
        xmlhttp.open("POST","main_page.php",false);
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
            return false
        }
        return true
    }
}