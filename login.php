<?php
/**
 * User: Chris Tran
 * Date: 6/12/2015
 * Time: 4:15 PM
 */

$host = 'localhost';
$user = 'root';
$pass = 'ntcsci571hw2';

$conn = mysql_connect($host, $user, $pass);

if (!$conn)
{
    die("Could not connect to database");
}


?>