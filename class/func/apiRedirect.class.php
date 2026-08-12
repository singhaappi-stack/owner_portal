<?php
/* 
    Objective: Api return function class
    Author: Sing
    LUD: [07 May 2023] Init

*/

class apiRedirect
{
    public function __construct($mode, $option = [])
    {
        switch ($mode) {
            case 'alert':
                echo "<script>alert('{$option['msg']}')</script>";
                break;
            case 'redirect':
                header("Location: {$option['url']}");
                echo "<script>window.location.href='{$option['url']}';</script>";
                exit();
                break;
            default:
                throw new Exception("Undefined redirect mode: $mode");
        }
    }
}
?>