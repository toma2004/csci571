/**
 * Created by NguyenTran on 6/6/2015.
 */

/*Function to add option based on user's input*/
var val = 0;
function createOption( usr_text )
{
    var myselect_list = document.getElementById('items');
    var optElement = document.createElement( "option" );
    optElement.text = usr_text.value;
    /*set value for option, just like when we have it in HTML*/
    optElement.value = val;
    val += 1;
    myselect_list.add(optElement);
}

/*Switch to second form*/
var isFirst = true;
function switch_form()
{
    if (validate_all())
    {
        var d1 = document.getElementById('form1');
        var d2 = document.getElementById('form2');

        if (isFirst)
        {
            d1.style.display = "none";
            d2.style.display = "block";
        }
        return true;
    }
    else
    {
        return false;
    }
}

/*Function called on the Submit button to validate all user's input before continue*/
function validate_all()
{
    var radio_element = document.getElementsByName('radio1');
    var checkbox_element = document.getElementsByName('checkbox1');
    var selList_element = document.getElementById('sel_text2');
    var fname_element = document.getElementById('fname');
    var lname_element = document.getElementById('lname');
    var mi_element = document.getElementById('mi');
    var mydob = document.getElementById('mydob');

    var myaddr = document.getElementById('myaddr');

    /*
    var mycity = document.getElementById('mycity');

    var mystate = document.getElementById('mystate');
     */

    var myzip = document.getElementById('myzip');

    var myphone = document.getElementById('myphone');

    var myemail = document.getElementById('myemail');
    var myerror = document.getElementById('error_msg');

    myerror.innerHTML = "";
    var isTrue = true;
    var radio_check = validate_radio(radio_element);
    var checkbox_check = validate_checkbox(checkbox_element);
    var selList_check = validate_selectionList(selList_element);
    var dob_check = validate_dob(mydob);


    if(fname_element.checkValidity() == false || lname_element.checkValidity() == false ||
        mi_element.checkValidity() == false)
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

    if(y < 0 || m < 0 || d < 0)
    {
        isOk = false;
    }
    return isOk;
}