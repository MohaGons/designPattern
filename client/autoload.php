<?php

function myAutoloader($class)
{
    $class = str_ireplace("App\\","",$class);
    $class = str_replace("\\","/",$class);

    if (file_exists($class.".class.php")) {

        include $class.".class.php";

    }
    
}

spl_autoload_register("App\myAutoloader");