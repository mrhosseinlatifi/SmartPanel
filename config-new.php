<?php

/******************************************************************************** 
 * Smart panel shop robot /  Programer: Mr.H.Latifi                *
 * Contact: hosseinlatifi50@gmail.com  -  telegram.me/mrhosseinlatifi     *
 ********************************************************************************/
// لایسنس
define('license', '*LIC*');
// توکن ربات
define('Token', '*TOKEN*');
// ایدی عددی ادمین ها
define(
    'admins',
    [
        '*ADMIN*',
    ]
);
// اطلاعات دیتابیس ربات 
define('localhost', 'localhost');
define('db_name', '*DBNAME*');
define('db_username', '*DBUSERNAME*');
define('db_password', '*DBPASSWORD*');
//------------Dont Edit-----------------//
//------------دست نزنید-----------------//
date_default_timezone_set("Asia/Tehran");
define('prefix', '*PERFIX*');
define('type_licenece', '*TYPELICENCE*');
define('sec_code', '*SEC*');
define("ROOTPATH", __DIR__);
define('debug', 0);
if (debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
}
ini_set('log_errors', TRUE);
ini_set('error_log', ROOTPATH . '/errors.log');
//------------Dont Edit-----------------//
//------------دست نزنید-----------------//
