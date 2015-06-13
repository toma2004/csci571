/**
 * Created by Chris Tran on 6/12/2015.
 */

/*Function to validate user's input to login and password*/
function validate_login_form1()
{
    var usrname = document.getElementById('usrname');
    var pwd = document.getElementById('pwd');
    var err = document.getElementById('error_message');
    err.innerHTML = '';

    if (usrname.checkValidity() == false || pwd.checkValidity() == false)
    {
        err.innerHTML += "Please enter a valid credentials";
    }
}