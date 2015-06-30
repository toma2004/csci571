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

/*Switch between forms and reports*/
function employee_transform( wherefrom, whereto )
{
    var d1 = document.getElementById('employee_page_form1');
    var d2 = document.getElementById('employee_page_add1');
    var d3 = document.getElementById('employee_page_add2_productCategory');
    var d4 = document.getElementById('employee_page_add2_products');
    var d5 = document.getElementById('employee_page_add3_specialsales');
    var d6 = document.getElementById('employee_page_modify1');
    var d7 = document.getElementById('employee_page_modify2');
    var d8 = document.getElementById('employee_page_modify3');
    var d9 = document.getElementById('employee_page_modify4');
    var d10 = document.getElementById('employee_page_delete1');
    var d11 = document.getElementById('employee_page_delete2');
    var d12 = document.getElementById('employee_page_delete3');
    var d13 = document.getElementById('employee_page_delete4')

    if ((wherefrom == "employee_page_form1" && whereto == "employee_page_add1") || (wherefrom == "employee_page_add2_productCategory" && whereto == "employee_page_add1") || (wherefrom == "employee_page_add2_products" && whereto == "employee_page_add1") || (wherefrom == "employee_page_add3_specialsales" && whereto == "employee_page_add1"))
    {
        d1.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d5.style.display = "none";
        d6.style.display = "none";
        d7.style.display = "none";
        d8.style.display = "none";
        d9.style.display = "none";
        d10.style.display = "none";
        d11.style.display = "none";
        d12.style.display = "none";
        d13.style.display = "none";
        d2.style.display = "block";
    }

    else if((wherefrom == "employee_page_add1" && whereto == "employee_page_add2_productCategory"))
    {
        d1.style.display = "none";
        d2.style.display = "none";
        d4.style.display = "none";
        d5.style.display = "none";
        d6.style.display = "none";
        d7.style.display = "none";
        d8.style.display = "none";
        d9.style.display = "none";
        d10.style.display = "none";
        d11.style.display = "none";
        d12.style.display = "none";
        d13.style.display = "none";
        d3.style.display = "block";
    }
    else if((wherefrom == "employee_page_add1" && whereto == "employee_page_add2_products"))
    {
        d2.style.display = "none";
        d3.style.display = "none";
        d1.style.display = "none";
        d5.style.display = "none";
        d6.style.display = "none";
        d7.style.display = "none";
        d8.style.display = "none";
        d9.style.display = "none";
        d10.style.display = "none";
        d11.style.display = "none";
        d12.style.display = "none";
        d13.style.display = "none";
        d4.style.display = "block";
    }
    else if(wherefrom == "employee_page_add2_productCategory" && whereto == "employee_page_form1" || (wherefrom == "employee_page_add1" && whereto == "employee_page_form1") || (wherefrom == "employee_page_add2_products" && whereto == "employee_page_form1") || (wherefrom == "employee_page_add3_specialsales" && whereto == "employee_page_form1") || (wherefrom == "employee_page_modify1" && whereto == "employee_page_form1") || (wherefrom == "employee_page_modify2" && whereto == "employee_page_form1") || (wherefrom == "employee_page_modify3" && whereto == "employee_page_form1") || (wherefrom == "employee_page_modify4" && whereto == "employee_page_form1") || (wherefrom == "employee_page_delete1" && whereto == "employee_page_form1") || (wherefrom == "employee_page_delete2" && whereto == "employee_page_form1") || (wherefrom == "employee_page_delete3" && whereto == "employee_page_form1") || (wherefrom == "employee_page_delete4" && whereto == "employee_page_form1"))
    {
        d2.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d5.style.display = "none";
        d6.style.display = "none";
        d7.style.display = "none";
        d8.style.display = "none";
        d9.style.display = "none";
        d10.style.display = "none";
        d11.style.display = "none";
        d12.style.display = "none";
        d13.style.display = "none";
        d1.style.display = "block";
    }
    else if(wherefrom == "employee_page_add1" && whereto == "employee_page_add3_specialsales")
    {
        d2.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d1.style.display = "none";
        d6.style.display = "none";
        d7.style.display = "none";
        d8.style.display = "none";
        d9.style.display = "none";
        d10.style.display = "none";
        d11.style.display = "none";
        d12.style.display = "none";
        d13.style.display = "none";
        d5.style.display = "block";
    }
    else if(wherefrom == "employee_page_form1" && whereto == "employee_page_modify1" || (wherefrom == "employee_page_modify2" && whereto == "employee_page_modify1") || (wherefrom == "employee_page_modify3" && whereto == "employee_page_modify1") || (wherefrom == "employee_page_modify4" && whereto == "employee_page_modify1"))
    {
        d2.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d1.style.display = "none";
        d5.style.display = "none";
        d7.style.display = "none";
        d8.style.display = "none";
        d9.style.display = "none";
        d10.style.display = "none";
        d11.style.display = "none";
        d12.style.display = "none";
        d13.style.display = "none";
        d6.style.display = "block";
    }
    else if(wherefrom == "employee_page_modify1" && whereto == "employee_page_modify2")
    {
        d2.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d1.style.display = "none";
        d5.style.display = "none";
        d6.style.display = "none";
        d8.style.display = "none";
        d9.style.display = "none";
        d10.style.display = "none";
        d11.style.display = "none";
        d12.style.display = "none";
        d13.style.display = "none";
        d7.style.display = "block";
    }
    else if(wherefrom == "employee_page_modify1" && whereto == "employee_page_modify3")
    {
        d2.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d1.style.display = "none";
        d5.style.display = "none";
        d7.style.display = "none";
        d6.style.display = "none";
        d9.style.display = "none";
        d10.style.display = "none";
        d11.style.display = "none";
        d12.style.display = "none";
        d13.style.display = "none";
        d8.style.display = "block";
    }
    else if(wherefrom == "employee_page_modify1" && whereto == "employee_page_modify4")
    {
        d2.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d1.style.display = "none";
        d5.style.display = "none";
        d7.style.display = "none";
        d8.style.display = "none";
        d6.style.display = "none";
        d10.style.display = "none";
        d11.style.display = "none";
        d12.style.display = "none";
        d13.style.display = "none";
        d9.style.display = "block";
    }
    else if(wherefrom == "employee_page_form1" && whereto == "employee_page_delete1" || wherefrom == "employee_page_delete2" && whereto == "employee_page_delete1" || (wherefrom == "employee_page_delete3" && whereto == "employee_page_delete1") || (wherefrom == "employee_page_delete4" && whereto == "employee_page_delete1"))
    {
        d2.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d1.style.display = "none";
        d5.style.display = "none";
        d7.style.display = "none";
        d8.style.display = "none";
        d6.style.display = "none";
        d9.style.display = "none";
        d11.style.display = "none";
        d12.style.display = "none";
        d13.style.display = "none";
        d10.style.display = "block";
    }
    else if(wherefrom == "employee_page_delete1" && whereto == "employee_page_delete2")
    {
        d2.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d1.style.display = "none";
        d5.style.display = "none";
        d7.style.display = "none";
        d8.style.display = "none";
        d6.style.display = "none";
        d9.style.display = "none";
        d10.style.display = "none";
        d12.style.display = "none";
        d13.style.display = "none";
        d11.style.display = "block";
    }
    else if(wherefrom == "employee_page_delete1" && whereto == "employee_page_delete3")
    {
        d2.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d1.style.display = "none";
        d5.style.display = "none";
        d7.style.display = "none";
        d8.style.display = "none";
        d6.style.display = "none";
        d9.style.display = "none";
        d10.style.display = "none";
        d11.style.display = "none";
        d13.style.display = "none";
        d12.style.display = "block";
    }
    else if(wherefrom == "employee_page_delete1" && whereto == "employee_page_delete4")
    {
        d2.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d1.style.display = "none";
        d5.style.display = "none";
        d7.style.display = "none";
        d8.style.display = "none";
        d6.style.display = "none";
        d9.style.display = "none";
        d10.style.display = "none";
        d11.style.display = "none";
        d12.style.display = "none";
        d13.style.display = "block";
    }
}

/*Func to validate and transform page admin add1*/
function validate_add1_transform()
{
    var radio_element = document.getElementsByName('add1_radio1');
    var check_radio = validate_radio(radio_element);
    var errmsg = document.getElementById('err_msg');
    errmsg.innerHTML = '';
    if(check_radio)
    {
        var value_checked = value_radio(radio_element);
        if (value_checked == "newproduct")
        {
            employee_transform('employee_page_add1','employee_page_add2_products');
        }
        else if(value_checked == "newcategory")
        {
            employee_transform('employee_page_add1','employee_page_add2_productCategory');
        }
        else if(value_checked == "newspecialsales")
        {
            employee_transform('employee_page_add1','employee_page_add3_specialsales');
        }
    }
    else
    {
        errmsg.innerHTML += "Please select one option before continue" + "<br/>";
    }
}

/*Function to validate input data on add role page*/
function validate_add_productCategory_page()
{
    isOk = true;
    var errmsg = document.getElementById('err_msg_add_productCategory');
    errmsg.innerHTML = '';

    var category_name = document.getElementById('category_name_id');
    var category_description = document.getElementById('category_description_id');

    if(category_name.checkValidity() == false)
    {
        errmsg.innerHTML += "Please enter a category name with valid format (no special characters)" + "<br/>";
        isOk = false;
    }
    if(category_description.value == '' || category_description.value == "Describe category here")
    {
        errmsg.innerHTML += "Please enter a category description" + "<br/>";
        isOk = false;
    }
    return isOk;
}

/*Function to validate input data on add employee page*/
function validate_add_product_page()
{
    var product_name = document.getElementById('product_name_id');
    var product_price = document.getElementById('product_price_id');

    var category_id = document.getElementById('product_category_add_id');


    var product_description = document.getElementById('product_description_id');
    var ingredient = document.getElementById('ingredient_id');
    var recipe = document.getElementById('recipe_id');

    var myerror = document.getElementById('err_msg_add_product');
    myerror.innerHTML = "";
    var isTrue = true;

    var image_upload = document.getElementById('product_image');

    if (product_name.checkValidity() == false)
    {
        myerror.innerHTML += "Please enter a valid product name (no special characters)" + "<br/>";
        isTrue = false;
    }

    if(product_price.checkValidity() == false)
    {
        myerror.innerHTML += "Please enter a valid product price (no $ sign in the beginning and only 2 digits after decimal point. Also price needs to be in range between 0-9999 inclusive)" + "<br/>";
        isTrue = false;
    }

    if(category_id.checkValidity() == false)
    {
        myerror.innerHTML += "Please enter a valid category id (number only)" + "<br/>";
        isTrue = false;
    }

    if(image_upload.checkValidity() == false)
    {
        myerror.innerHTML += "Please select an image to upload" + "<br/>";
        isTrue = false;
    }

    if(product_description.value == '' || product_description.value == "Describe product here")
    {
        myerror.innerHTML += "Please enter product description" + "<br/>";
        isTrue = false;
    }

    if(ingredient.value == '' || ingredient.value == "Enter ingredients info here")
    {
        myerror.innerHTML += "Please enter ingredients" + "<br/>";
        isTrue = false;
    }

    if(recipe.value == '' || recipe.value == "Enter recipe here")
    {
        myerror.innerHTML += "Please enter recipe" + "<br/>";
        isTrue = false;
    }

    return isTrue;
}

/*Function to validate employee id to be modified*/
function validate_modify_page_1()
{
    var employee_id = document.getElementById('employee_id_modify_1');
    var errmsg = document.getElementById('err_msg_modify1');
    errmsg.innerHTML = '';
    if (employee_id.checkValidity() == false)
    {
        errmsg.innerHTML += "Please enter a valid employee id" + "<br/>";
        return false;
    }
    else
    {
        return true;
    }
}

/*Function to validate employee id to be modified*/
function validate_delete_page_1()
{
    var employee_id = document.getElementById('employee_id_delete_1');
    var errmsg = document.getElementById('err_msg_delete1');
    errmsg.innerHTML = '';
    if (employee_id.checkValidity() == false)
    {
        errmsg.innerHTML += "Please enter a valid employee id" + "<br/>";
        return false;
    }
    else
    {
        return true;
    }
}

/*Function to validate form data in modify*/
function validate_modify_page2()
{
    var fname_element = document.getElementById('modified_fname');
    var lname_element = document.getElementById('modified_lname');

    var myaddr = document.getElementById('modified_myaddr');
    var mycity = document.getElementById('modified_mycity');
    var mystate = document.getElementById('modified_mystate');
    var mycountry = document.getElementById('modified_mycountry');

    var mydob = document.getElementById('modified_mydob');
    var mysalary = document.getElementById('modified_mysalary');


    var myphone = document.getElementById('modified_myphone');
    var myemail = document.getElementById('modified_myemail');

    var user_name = document.getElementById('modified_myusername');
    var pass_word = document.getElementById('modified_mypwd');

    var marriage_status = document.getElementsByName('admin_modified_radio1');
    var mygender = document.getElementsByName('admin_modified_radio2');
    var usertype = document.getElementsByName('admin_modified_cb1[]');

    var marriage_status_check = validate_radio(marriage_status);
    var mygender_check = validate_radio(mygender);

    var usertype_check = validate_checkbox(usertype);

    var myerror = document.getElementById('modified_page2_errmsg');
    myerror.innerHTML = "";
    var isTrue = true;

    if(mydob.value == '' && fname_element.value == '' && lname_element.value == '' && mycity.value == '' && mystate.value == '' && user_name.value == '' && pass_word.value == '' && mysalary.value == '' && myaddr.value == '' && mycountry.value == '' && myphone.value == '' && myemail.value == '')
    {
        if(!marriage_status_check && !mygender_check && !usertype_check)
        {
            /*Admin did not make any changes. Don't submit the form*/
            myerror.innerHTML += "You have not made any changes" + "<br/>";
            isTrue = false;
        }
    }

    if(mydob.value != '')
    {
        var dob_check = validate_dob(mydob);
        /*Check date of birth*/
        if (dob_check == false)
        {
            myerror.innerHTML += "Please enter a correct date of birth" + "<br/>";
            isTrue = false;
        }
    }

    if(fname_element.value != '')
    {
        if(fname_element.checkValidity() == false)
        {
            myerror.innerHTML += "First name can't contain number" + "<br/>";
            isTrue = false;
        }
    }
    if(lname_element.value != '')
    {
        if(lname_element.checkValidity() == false)
        {
            myerror.innerHTML += "Last name can't contain number" + "<br/>";
            isTrue = false;
        }
    }
    if(mycity.value != '')
    {
        if(mycity.checkValidity() == false)
        {
            myerror.innerHTML += "City can't contain number" + "<br/>";
            isTrue = false;
        }
    }

    if(mystate.value != '')
    {
        if(mystate.checkValidity() == false)
        {
            myerror.innerHTML += "State must be a two-characters abbreviation" + "<br/>";
            isTrue = false;
        }
    }

    if(user_name.value != '')
    {
        if(user_name.checkValidity() == false)
        {
            myerror.innerHTML += "Please enter a correct user name which can't contain any special characters" + "<br/>";
            isTrue = false;
        }
    }
    if(pass_word.value != '')
    {
        if(pass_word.checkValidity() == false)
        {
            myerror.innerHTML += "Please enter a correct password format which can't contain any special characters" + "<br/>";
            isTrue = false;
        }
    }

    if(mysalary.value != '')
    {
        if(mysalary.checkValidity() == false)
        {
            myerror.innerHTML += "Please enter a correct salary" + "<br/>";
            isTrue = false;
        }
    }
    if(myaddr.value != '')
    {
        if(myaddr.checkValidity() == false)
        {
            myerror.innerHTML += "Please enter a correct street address format. Street number followed by a space and then street name" + "<br/>";
            isTrue = false;
        }
    }


    if(mycountry.value != '')
    {
        if(mycountry.checkValidity() == false)
        {
            myerror.innerHTML += "Please enter a correct country name" + "<br/>";
            isTrue = false;
        }
    }
    if(myphone.value != '')
    {
        if(myphone.checkValidity() == false)
        {
            myerror.innerHTML += "Please enter a correct phone number" + "<br/>";
            isTrue = false;
        }
    }

    if(myemail.value != '')
    {
        if(myemail.checkValidity() == false)
        {
            myerror.innerHTML += "Please enter a correct email address" + "<br/>";
            isTrue = false;
        }
    }

    return isTrue;
}

/*Clear default text in textarea when user focus cursor*/
function clearText(page,text_num)
{
    var myval = document.getElementById('category_description_id');
    var text_product_description = document.getElementById('product_description_id');
    var text_ingredient = document.getElementById('ingredient_id');
    var text_recipe = document.getElementById('recipe_id');
    if(page == "product_category_page")
    {
        if (myval.value == "Describe category here") {
            myval.value = '';
        }
    }
    else if (page == "product_page")
    {
        if(text_num == "text1")
        {
            if(text_product_description.value == "Describe product here")
            {
                text_product_description.value = '';
            }
        }
        else if(text_num == "text2")
        {
            if(text_ingredient.value == "Enter ingredients info here")
            {
                text_ingredient.value = '';
            }
        }
        else if(text_num == "text3")
        {
            if(text_recipe.value == "Enter recipe here")
            {
                text_recipe.value = '';
            }
        }
    }
}

/*Function to validate form data for special sales*/
function validate_add_special_sales_page()
{
    var product_id = document.getElementById('product_specialsales_id');

    var start_date = document.getElementById('mystart_date_id');

    var end_date = document.getElementById('myend_date_id');

    var percentage_discount = document.getElementById('percentage_discount_id');

    var myerror = document.getElementById('err_msg_add_specialsales');
    myerror.innerHTML = "";
    var isTrue = true;

    /*validate dates*/
    if (!check_date_before(start_date,end_date)) /*start date is after end date*/
    {
        myerror.innerHTML += "Start date can't be after end date. Please change start date to a different value" + "<br/>";
        isTrue = false;
    }

    if(product_id.checkValidity() == false)
    {
        myerror.innerHTML += "Please enter only integer number for product id" + "<br/>";
        isTrue = false;
    }

    if(percentage_discount.checkValidity() == false)
    {
        myerror.innerHTML += "Please enter a correct format for percentage discount (no % sign at the end and only 2 digits after decimal point. Also the percentage needs to be in range between 0-100 inclusive)" + "<br/>";
        isTrue = false;
    }
    return isTrue;
}

/*Function to validate modify page 1. Ensure user select 1 radio button before continue*/
function validate_modified1_transform()
{
    var radio_element = document.getElementsByName('modify1_radio1');
    var check_radio = validate_radio(radio_element);
    var errmsg = document.getElementById('err_msg_modify1');
    errmsg.innerHTML = '';
    if(check_radio)
    {
        var value_checked = value_radio(radio_element);
        if (value_checked == "product")
        {
            employee_transform('employee_page_modify1','employee_page_modify2');
        }
        else if(value_checked == "category")
        {
            employee_transform('employee_page_modify1','employee_page_modify3');
        }
        else if(value_checked == "specialsales")
        {
            employee_transform('employee_page_modify1','employee_page_modify4');
        }
    }
    else
    {
        errmsg.innerHTML += "Please select one option before continue" + "<br/>";
    }
}

/*Function to validate product id to be modified*/
function validate_modified2_productID()
{
    var product_id = document.getElementById('product_modified_id');
    var errmsg = document.getElementById('err_msg_modify2');
    errmsg.innerHTML = '';
    if (product_id.checkValidity() == false)
    {
        errmsg.innerHTML += "Please enter a valid product id" + "<br/>";
        return false;
    }
    else
    {
        return true;
    }
}

/*Function to validate category id to be modified*/
function validate_modified3_categoryID()
{
    var category_id = document.getElementById('category_modified_id');
    var errmsg = document.getElementById('err_msg_modify3');
    errmsg.innerHTML = '';
    if (category_id.checkValidity() == false)
    {
        errmsg.innerHTML += "Please enter a valid category id" + "<br/>";
        return false;
    }
    else
    {
        return true;
    }
}

/*Function to validate special sale id to be modified*/
function validate_modified4_specialsaleID()
{
    var specialsale_id = document.getElementById('specialsale_modified_id');
    var errmsg = document.getElementById('err_msg_modify4');
    errmsg.innerHTML = '';
    if (specialsale_id.checkValidity() == false)
    {
        errmsg.innerHTML += "Please enter a valid special sale id" + "<br/>";
        return false;
    }
    else
    {
        return true;
    }
}

/*Function to validate form data in modify*/
function validate_modify2_product()
{
    var product_name = document.getElementById('modified_product_name');
    var product_price = document.getElementById('modified_product_price');

    var produce_description = document.getElementById('modified_product_description');
    var ingredient = document.getElementById('modified_ingredients');
    var recipe = document.getElementById('modified_recipe');

    var category_id_product_belong_to = document.getElementsByName('employee_modified2_product_cb1[]');
    var category_id_product_belong_to_check = validate_checkbox(category_id_product_belong_to);

    var specialsale_id_product_belong_to = document.getElementsByName('employee_modified2_product_radio1');
    var specialsale_id_product_belong_to_check = validate_radio(specialsale_id_product_belong_to);

    var myerror = document.getElementById('employee_page_modify2_product_errmsg');
    myerror.innerHTML = "";
    var isTrue = true;

    if(product_name.value == '' && product_price.value == '' && produce_description.value == '' && ingredient.value == '' && recipe.value == '')
    {
        if(!category_id_product_belong_to_check && !specialsale_id_product_belong_to_check)
        {
            /*Employee did not make any changes. Don't submit the form*/
            myerror.innerHTML += "You have not made any changes" + "<br/>";
            isTrue = false;
        }
    }

    if(product_name.value != '')
    {
        if(product_name.checkValidity() == false)
        {
            myerror.innerHTML += "Product name can't contain special characters" + "<br/>";
            isTrue = false;
        }
    }
    if(product_price.value != '')
    {
        if(product_price.checkValidity() == false)
        {
            myerror.innerHTML += "Please enter a valid product price (no $ sign in the beginning and only 2 digits after decimal point. Also price needs to be in range between 0-9999 inclusive)" + "<br/>";
            isTrue = false;
        }
    }
    return isTrue;
}

/*Function to validate form data for category modification*/
function validate_modify3_category()
{
    var category_name = document.getElementById('modified_category_name');

    var category_description = document.getElementById('modified_category_description');

    var myerror = document.getElementById('employee_page_modify3_category_errmsg');
    myerror.innerHTML = "";
    var isTrue = true;

    if(category_name.value == '' && category_description.value == '')
    {
        /*Employee did not make any changes. Don't submit the form*/
        myerror.innerHTML += "You have not made any changes" + "<br/>";
        isTrue = false;
    }

    if(category_name.value != '')
    {
        if(category_name.checkValidity() == false)
        {
            myerror.innerHTML += "Category name can't contain special characters" + "<br/>";
            isTrue = false;
        }
    }
    return isTrue;
}

/*Function to validate form data for special sale modification*/
function validate_modify4_specialsale()
{
    var start_date = document.getElementById('modified_specialsale_start_date');

    var end_date = document.getElementById('modified_specialsale_end_date');

    var percentage_discount = document.getElementById('modified_specialsale_percentage_discount');

    var product_id_checkbox = document.getElementsByName('employee_modified4_special_sale_cb1[]');
    var product_id_checkbox_to_check = validate_checkbox(product_id_checkbox);


    var myerror = document.getElementById('employee_page_modify4_specialsale_errmsg');
    myerror.innerHTML = "";
    var isTrue = true;


    if (start_date.value == '' && end_date.value == '' && percentage_discount.value == '' && product_id_checkbox_to_check == false)
    {
        /*Employee did not make any changes. Don't submit the form*/
        myerror.innerHTML += "You have not made any changes" + "<br/>";
        isTrue = false;
    }
    /*They can just change either start or end date. Need to check with original values to ensure correctness*/
    /*If the employee changes both dates*/
    if (start_date.value != '' && end_date.value != '')
    {
        if (!check_date_before(start_date,end_date)) /*start date is after end date*/
        {
            myerror.innerHTML += "Start date can't be after end date. Please change start date to a different value" + "<br/>";
            isTrue = false;
        }
    }


    if(percentage_discount.value != '')
    {
        if(percentage_discount.checkValidity() == false)
        {
            myerror.innerHTML += "Please enter a correct format for percentage discount (no % sign at the end and only 2 digits after decimal point. Also the percentage needs to be in range between 0-100 inclusive)" + "<br/>";
            isTrue = false;
        }
    }
    return isTrue;
}

/*Function to validate employee delete page 1*/
function validate_delete1_transform()
{
    var radio_element = document.getElementsByName('delete1_radio1');
    var check_radio = validate_radio(radio_element);
    var errmsg = document.getElementById('err_msg_delete1');
    errmsg.innerHTML = '';
    if(check_radio)
    {
        var value_checked = value_radio(radio_element);
        if (value_checked == "product")
        {
            employee_transform('employee_page_delete1','employee_page_delete2');
        }
        else if(value_checked == "category")
        {
            employee_transform('employee_page_delete1','employee_page_delete3');
        }
        else if(value_checked == "specialsales")
        {
            employee_transform('employee_page_delete1','employee_page_delete4');
        }
    }
    else
    {
        errmsg.innerHTML += "Please select one option before continue" + "<br/>";
    }
}

/*Function to validate employee delete page 2 - deleting product*/
function validate_delete2_productID()
{
    var product_id = document.getElementById('delete_product_id');
    var errmsg = document.getElementById('err_msg_delete2_product');
    errmsg.innerHTML = '';
    if (product_id.checkValidity() == false)
    {
        errmsg.innerHTML += "Please enter a valid product id" + "<br/>";
        return false;
    }
    else
    {
        return true;
    }
}

/*Function to validate employee delete page 3 - deleting category*/
function validate_delete3_categoryID()
{
    var category_id = document.getElementById('delete_category_id');
    var errmsg = document.getElementById('err_msg_delete3_category');
    errmsg.innerHTML = '';
    if (category_id.checkValidity() == false)
    {
        errmsg.innerHTML += "Please enter a valid category id" + "<br/>";
        return false;
    }
    else
    {
        return true;
    }
}

/*Function to validate employee delete page 4 - deleting special sale*/
function validate_delete4_specialsaleID()
{
    var specialsale_id = document.getElementById('delete_specialsale_id');
    var errmsg = document.getElementById('err_msg_delete4_specialsale');
    errmsg.innerHTML = '';
    if (specialsale_id.checkValidity() == false)
    {
        errmsg.innerHTML += "Please enter a valid specials sale id" + "<br/>";
        return false;
    }
    else
    {
        return true;
    }
}