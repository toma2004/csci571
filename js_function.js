/**
 * Created by NguyenTran on 6/6/2015.
 */

/*Function to add option based on user's input on form 1*/
var val = 1;
var usr_arr = [];
var myindex;
/*Variable from form1*/
var radio_element = document.getElementsByName('radio1');
var radio_element2 = document.getElementsByName('radio2');
var checkbox_element = document.getElementsByName('checkbox1');
var checkbox_element2 = document.getElementsByName('checkbox2');
var selList_element = document.getElementById('sel_text2');
var selList_element2 = document.getElementById('items');
var fname_element = document.getElementById('fname');
var lname_element = document.getElementById('lname');
var mi_element = document.getElementById('mi');
var mydob = document.getElementById('mydob');
var myaddr = document.getElementById('myaddr');
var myapt = document.getElementById('myapt');
var mycity = document.getElementById('mycity');
var mystate = document.getElementById('mystate');
var myzip = document.getElementById('myzip');
var myphone = document.getElementById('myphone');
var myemail = document.getElementById('myemail');
var user_name = document.getElementById('username');
var pass_word = document.getElementById('pwd');
/*********************************************************************/
/*variable from form 2*/
var mysel_list1_form2 = document.getElementById('form2_sel_text');
var mysel_list_form2 = document.getElementById('form2_sel_text2');
var myradio_form2 = document.getElementsByName('form2_radio1');
var myradio2_form2 = document.getElementsByName('form2_radio2');
var mycheckbox_form2 = document.getElementsByName('form2_checkbox1');
var mycheckbox2_form2 = document.getElementsByName('form2_checkbox2');
var mytextarea = document.getElementById('mycomment');
/*********************************************************************/
function createOption( usr_text )
{
    var myselect_list = document.getElementById('items');
    var optElement = document.createElement( "option" );
    optElement.text = usr_text.value;
    /*set value for option, just like when we have it in HTML*/
    val += 1;
    optElement.value = val;
    myselect_list.add(optElement);
}

/*Function to remove all items in the select list that user dynamically created*/
function removeOption()
{
    var myselect_list = document.getElementById('items');
    while(myselect_list.length > 1)
    {
        if(myselect_list[myselect_list.length-1] != 1)
        {
            myselect_list.remove(myselect_list.length-1);
        }
    }
    val = 1;
}

/*Function to add option based on user's input on form 2*/
var val_form2 = 1;
function createOption_form2( usr_text )
{
    var myselect_list = document.getElementById('form2_items');
    var optElement = document.createElement( "option" );
    optElement.text = usr_text.value;
    /*set value for option, just like when we have it in HTML*/
    val_form2 += 1;
    optElement.value = val_form2;
    myselect_list.add(optElement);
}

/*Function to remove all items in the select list that user dynamically created for form 2*/
function removeOption_form2()
{
    var myselect_list = document.getElementById('form2_items');
    while(myselect_list.length > 1)
    {
        if(myselect_list[myselect_list.length-1] != 1)
        {
            myselect_list.remove(myselect_list.length-1);
        }
    }
    val_form2 = 1;
}

/*Switch between forms and reports*/
function switch_form( wherefrom, whereto )
{
    var d1 = document.getElementById('form1');
    var d2 = document.getElementById('form2');
    var d3 = document.getElementById('report1');
    var d4 = document.getElementById('report2');

    if (wherefrom == "form1" && whereto == "form2")
    {
        if (validate_all())
        {
            d1.style.display = "none";
            d3.style.display = "none";
            d4.style.display = "none";
            d2.style.display = "block";
            return true;
        }
        else
        {
            return false;
        }
    }
    else if((wherefrom == "form1" && whereto == "report1") || (wherefrom == "form2" && whereto == "report1") || (wherefrom == "report2" && whereto == "report1"))
    {
        d1.style.display = "none";
        d2.style.display = "none";
        d4.style.display = "none";
        d3.style.display = "block";
        return true;
    }
    else if((wherefrom == "form2" && whereto == "form1") || (wherefrom == "report1" && whereto == "form1") || (wherefrom == "report2" && whereto == "form1"))
    {
        d2.style.display = "none";
        d3.style.display = "none";
        d4.style.display = "none";
        d1.style.display = "block";
        return true;
    }
    else if(wherefrom == "report1" && whereto == "report2")
    {
        d2.style.display = "none";
        d3.style.display = "none";
        d1.style.display = "none";
        d4.style.display = "block";
        return true;
    }
}

/*Helper function when moving from form 2 to report 1
Need to validate form 2
 */
function switch_form_form2( wherefrom, whereto )
{

    var myoutput = validate_second_form();
    var myform1 = document.getElementById('myform1');
    var myform2 = document.getElementById('myform2');

    if (myoutput)
    {
        addNewUser();
        /*Append new uer info to report 1*/
        print_info_report1();
        /*Switch to report 1 div*/
        var output = switch_form( wherefrom, whereto );

        /*Reset form 1 and 2*/
        myform1.reset();
        myform2.reset();
        removeOption();
        removeOption_form2();
    }
    return myoutput;
}

/*Function to validate user's input in second form*/
function validate_second_form()
{
    mysel_list_form2 = document.getElementById('form2_sel_text2');
    myradio_form2 = document.getElementsByName('form2_radio1');
    mycheckbox_form2 = document.getElementsByName('form2_checkbox1');

    mysel_list1_form2 = document.getElementById('form2_sel_text');

    myradio2_form2 = document.getElementsByName('form2_radio2');

    mycheckbox2_form2 = document.getElementsByName('form2_checkbox2');

    mytextarea = document.getElementById('mycomment');


    var isTrue = true;
    var myerror = document.getElementById('error_msg2');
    myerror.innerHTML = '';

    if(validate_checkbox(mycheckbox_form2) == false)
    {
        myerror.innerHTML += "Please select at least 1 checkbox in checkbox1." + "<br/>";
        isTrue = false;
    }
    if(validate_checkbox(mycheckbox2_form2) == false)
    {
        myerror.innerHTML += "Please select at least 1 checkbox in checkbox2." + "<br/>";
        isTrue = false;
    }
    if(validate_radio(myradio_form2) == false)
    {
        myerror.innerHTML += "Please select at least 1 radio in radio1." + "<br/>";
        isTrue = false;
    }
    if(validate_radio(myradio2_form2) == false)
    {
        myerror.innerHTML += "Please select at least 1 radio in radio2." + "<br/>";
        isTrue = false;
    }
    if(validate_selectionList(mysel_list_form2) == false)
    {
        myerror.innerHTML += "Please select at least 1 item in selection list2." + "<br/>";
        isTrue = false;
    }
    if(validate_selectionList(mysel_list1_form2) == false)
    {
        myerror.innerHTML += "Please select at least 1 item in selection list1." + "<br/>";
        isTrue = false;
    }
    return isTrue;
}

/*Function called on the Continue button to validate all user's input in first form*/
function validate_all()
{
     radio_element = document.getElementsByName('radio1');
     radio_element2 = document.getElementsByName('radio2');
     checkbox_element = document.getElementsByName('checkbox1');
     checkbox_element2 = document.getElementsByName('checkbox2');
     selList_element = document.getElementById('sel_text2');
     selList_element2 = document.getElementById('items');
     fname_element = document.getElementById('fname');
     lname_element = document.getElementById('lname');
     mi_element = document.getElementById('mi');
     mydob = document.getElementById('mydob');
     myaddr = document.getElementById('myaddr');
     myapt = document.getElementById('myapt');
     mycity = document.getElementById('mycity');
     mystate = document.getElementById('mystate');
     myzip = document.getElementById('myzip');
     myphone = document.getElementById('myphone');
     myemail = document.getElementById('myemail');
     user_name = document.getElementById('username');
     pass_word = document.getElementById('pwd');
     var myerror = document.getElementById('error_msg');

    myerror.innerHTML = "";
    var isTrue = true;
    var radio_check = validate_radio(radio_element);
    var checkbox_check = validate_checkbox(checkbox_element);
    var selList_check = validate_selectionList(selList_element);
    var dob_check = validate_dob(mydob);

    if ((user_name.checkValidity() == false) || (pass_word.checkValidity() == false))
    {
        myerror.innerHTML += "Please enter an user name and/or password" + "<br/>";
        isTrue = false;
    }

    if((fname_element.checkValidity() == false) || (lname_element.checkValidity() == false) || (mi_element.checkValidity() == false) || (mycity.checkValidity() == false) || (mystate.checkValidity() == false))
    {
        myerror.innerHTML += "First name/Last name/Middle initial/City/State can't contain number" + "<br/>";
        isTrue = false;
    }

    if(myaddr.checkValidity() == false)
    {
        myerror.innerHTML += "Please enter a correct address" + "<br/>";
        isTrue = false;
    }

    if(myzip.checkValidity() == false)
    {
        myerror.innerHTML += "Please enter a correct zip code with 5 digit numbers" + "<br/>";
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

    /*Check radio button of radio1 */
    if (radio_check == false)
    {
        myerror.innerHTML += "Please select at least 1 radio button in radio1." + "<br/>";
        isTrue = false;
    }

    /*Check checkbox of checkbox1 */
    if (checkbox_check == false)
    {
        myerror.innerHTML += "Please select at least 1 checkbox in checkbox1." + "<br/>";
        isTrue = false;
    }

    /*Check selection list of list 1 */
    if (selList_check == false)
    {
        myerror.innerHTML += "Please select at least 1 element in the selection list 1." + "<br/>";
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

/*Function to validate a selection list to at least 1 selected */
function validate_selectionList( selList_element )
{
    var isOk = false;
    for (var i = 0; i < selList_element.length; i++)
    {
        if (selList_element[i].selected)
        {
            isOk = true;
            break;
        }
    }
    return isOk;
}

/*Function to check date of birth*/
function validate_dob( mydob )
{
    var isOk = true;
    var today = new Date();
    var user_birthday = new Date(mydob.value.replace(/(\d{4})[\/-](\d{1,2})[\/-](\d{1,2})/, "$2/$3/$1"));
    var y = today.getYear() - user_birthday.getYear();
    var m = today.getMonth() - user_birthday.getMonth();
    var d = today.getDate() - user_birthday.getDate();

    if(y < 0)
    {
        isOk = false;
    }
    else if (y == 0)
    {
        if (m < 0)
        {
            isOk = false;
        }
        else if (m == 0)
        {
            if (d < 0)
            {
                isOk = false;
            }
        }
    }
    return isOk;
}

/*Clear default text in textarea when user focus cursor*/
function clearText()
{
    var myval = document.getElementById('mycomment');
    if (myval.value == "Comments")
    {
        myval.value='';
    }
}

/*Constructor for every new user*/
function User(fn,ln,mi,street,apt,city,state,zip,dob,phone,email_addr,usr,pass,list1,list2,radio1,radio2,check1,check2,list1_form2,list2_form2,radio1_form2,radio2_form2,check1_form2,check2_form2,textarea_form2)
{
    this.fn = fn;
    this.ln = ln;
    this.mi = mi;
    this.street = street;
    this.apt = apt;
    this.city = city;
    this.state = state;
    this.zip = zip;
    this.dob = dob;
    this.phone = phone;
    this.email_addr = email_addr;
    this.usr = usr;
    this.pass = pass;
    this.list1 = value_selectList(list1).slice();
    this.list2 = value_selectList(list2).slice();
    this.radio1 = value_radio(radio1);
    this.radio2 = value_radio(radio2);
    this.check1 = value_checkboxes(check1).slice();
    this.check2 = value_checkboxes(check2).slice();
    /*form 2 starts here */
    this.list1_form2 = value_selectList(list1_form2).slice();
    this.list2_form2 = value_selectList(list2_form2).slice();
    this.radio1_form2 = value_radio(radio1_form2);
    this.radio2_form2 = value_radio(radio2_form2);
    this.check1_form2 = value_checkboxes(check1_form2).slice();
    this.check2_form2 = value_checkboxes(check2_form2).slice();
    this.textarea_form2 = textarea_form2;
}

/*print short info for form 1*/
function print_info_report1()
{
    var myp = document.getElementById('info');

    if (usr_arr.length > 0)
    {
        myindex = usr_arr.length - 1;
        var usr_obj = usr_arr[myindex];
        myp.innerHTML += usr_obj.fn + " " + usr_obj.ln + ", " + usr_obj.usr + "... ";
        myp.innerHTML += '<input type="button" value="Click here for more info" onClick="print_all_info(' + myindex + ')"/>' + '<br/>';
    }
}

/*function to print all information of a particular user*/
function print_all_info( index )
{
    var myusr_obj = usr_arr[index];
    var myh = document.getElementById('header_4');
    var myp = document.getElementById('all_info');
    myp.innerHTML ='';
    myh.innerHTML = '';


    /*Label header*/
    myh.innerHTML += "Our record indicated that user " + myusr_obj.usr + " has entered the following information: ";

    /*Print all information about a user*/
    myp.innerHTML += "First name: " + myusr_obj.fn + "  | Last name: " + myusr_obj.ln + "  | Middle Initial: " + myusr_obj.mi + "<br/>";
    if (myusr_obj.apt != '')
    {
        myp.innerHTML += "Address info: " + myusr_obj.street + ", APT/Suite #" + myusr_obj.apt + ", " + myusr_obj.city + ", " + myusr_obj.state + " " + myusr_obj.zip + "<br/>";
    }
    else
    {
        myp.innerHTML += "Address info: " + myusr_obj.street + ", " + myusr_obj.city + ", " + myusr_obj.state + " " + myusr_obj.zip + "<br/>";
    }

    if (myusr_obj.phone != '')
    {
        myp.innerHTML += "Birthday date: " + myusr_obj.dob.replace(/(\d{4})[\/-](\d{1,2})[\/-](\d{1,2})/, "$2/$3/$1") + "<br/>" + "Phone number: " + myusr_obj.phone + "<br/>" + "Email address: " + myusr_obj.email_addr + "<br/>";
    }
    else
    {
        myp.innerHTML += "Birthday date: " + myusr_obj.dob.replace(/(\d{4})[\/-](\d{1,2})[\/-](\d{1,2})/, "$2/$3/$1") + "<br/>" + "Email address: " + myusr_obj.email_addr + "<br/>";
    }

    myp.innerHTML += "Username: " + myusr_obj.usr + "  | Password: " + myusr_obj.pass + "<br/>";

    /*Getting values for select lists, radio, and checkboxes*/

    /*Display*/
    print_to_html(myp,myusr_obj.list1,'select list 1');
    print_to_html(myp,myusr_obj.list2,'select list 2');

    if (myusr_obj.radio1 != 'none')
    {
        myp.innerHTML += "Your selects for radio 1: " + myusr_obj.radio1 + "<br/>";
    }
    if (myusr_obj.radio2 != 'none')
    {
        myp.innerHTML += "Your selects for radio 2: " + myusr_obj.radio2 + "<br/>";
    }

    print_to_html(myp,myusr_obj.check1,'checkbox 1');
    print_to_html(myp,myusr_obj.check2,'checkbox 2');

    /*Start to display info from form 2*/
    print_to_html(myp,myusr_obj.list1_form2,'select list 1 form 2: ');
    print_to_html(myp,myusr_obj.list2_form2,'select list 2 form 2: ');
    if (myusr_obj.radio1_form2 != 'none')
    {
        myp.innerHTML += "Your selects for radio 1 form 2: " + myusr_obj.radio1_form2 + "<br/>";
    }
    if (myusr_obj.radio2_form2 != 'none')
    {
        myp.innerHTML += "Your selects for radio 2 form 2: " + myusr_obj.radio2_form2 + "<br/>";
    }
    print_to_html(myp,myusr_obj.check1_form2,'checkbox 1 form 2');
    print_to_html(myp,myusr_obj.check2_form2,'checkbox 2 form 2');

    if (myusr_obj.textarea_form2 != "Comments")
    {
        myp.innerHTML += "Your additional comments: " + "<br/>" + myusr_obj.textarea_form2;
    }


    /*Have to display after print*/
    switch_form('report1','report2');
}

/*Function to add new user object to my global array*/
function addNewUser()
{
    var new_user = new User(fname_element.value,lname_element.value,mi_element.value,myaddr.value,myapt.value,mycity.value,mystate.value,myzip.value,mydob.value,myphone.value,myemail.value,user_name.value,pass_word.value,selList_element2,selList_element,radio_element,radio_element2,checkbox_element,checkbox_element2,mysel_list1_form2,mysel_list_form2,myradio_form2,myradio2_form2,mycheckbox_form2,mycheckbox2_form2,mytextarea.value);
    usr_arr.push(new_user);
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

/*Function to obtain values of select list
* multiple values might or might not be allowed depending on how it's set up in HTML*/
function value_selectList( sel_list_element )
{
    var initial = [];
    for (var i = 0; i < sel_list_element.length; i++)
    {
        if (sel_list_element[i].selected)
        {
            initial.push(sel_list_element[i].value);
        }
    }
    return initial;
}

/*Function to print element in an array to inner HTML element*/
function print_to_html( innerHTML_element, arr, name_display )
{
    if (arr.length > 0)
    {
        innerHTML_element.innerHTML += "Your selection from " + name_display + ": ";
        for (var i = 0; i < arr.length; i++)
        {
            if (i+1 == arr.length)
            {
                innerHTML_element.innerHTML += arr[i] + "<br/>";
            }
            else
            {
                innerHTML_element.innerHTML += arr[i] + ", ";
            }
        }
    }
}