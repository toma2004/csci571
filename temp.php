<?php
/**
 * Created by PhpStorm.
 * User: NguyenTran
 * Date: 6/19/2015
 * Time: 9:41 PM
 */

$myarr = array("1","2","3","4");
$sub_arr = array("1","2");




if (!check_subset($sub_arr,$myarr))
{
    echo "FAILED";
}
else
{
    echo "PASSED";
}

/*Function to check if an array is a subset of another larger array*/
function check_subset($arr,$sorted_larger_arr)
{
    $total_element = count($sorted_larger_arr);
    foreach ($arr as $arr_val)
    {
/*        if(!binarySearch($sorted_larger_arr,0,$total_element-1,$arr_val))
        {
            return false;
            break;
        } */
        $temp = binarySearch($sorted_larger_arr,0,$total_element-1,$arr_val);
        if ($temp == false)
        {
            return false;
        }
    }
    return true;
}
/*binary search*/
function binarySearch($arr,$first,$last,$val)
{
    if ($first > $last)
    {
        return false;
    }
    $mid = floor(($first+$last)/2);
    echo "my mid ".$mid;
    echo "arr mid ".$arr[(int)$mid];
    echo "my val ".$val;
    if ($arr[(int)$mid] == $val)
    {
        echo "return true";
        return true;
    }
    elseif ($arr[(int)$mid] > $val)
    {
        return binarySearch($arr,$first,$mid-1,$val);
    }
    else
    {
        return binarySearch($arr,$mid+1,$last,$val);
    }
}