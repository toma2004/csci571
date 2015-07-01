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
}

/*function to receive response from server and display special sale on main page*/
function display_result_special_sale_main_page()
{
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
    {
        document.getElementById('main_page_form').innerHTML = xmlhttp.responseText;
    }
}