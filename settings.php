<?php
error_reporting(0);
if(!defined(APP)){
    die("You are not allowed to be here...");
}
error_reporting(1);
//echo $_SERVER['SERVER_NAME'];

if('elmqvist.org' == $_SERVER['SERVER_NAME'] || 
   'www.elmqvist.org' == $_SERVER['SERVER_NAME'] ||
   'vulpinusminima.se' == $_SERVER['SERVER_NAME'] ||
   'www.vulpinusminima.se' == $_SERVER['SERVER_NAME']
  )
{
    define("DB_SERVERNAME","mysql33.unoeuro.com");
    define("DB_USERNAME","elmqvist_org");
    define("DB_PASSWORD","fe2c43pb");
    define("DB_NAME","elmqvist_org_db_volupinusminima");
}
else
{
    define("DB_SERVERNAME","mysql-server");
    define("DB_USERNAME","elmqvist_org");
    define("DB_PASSWORD","fe2c43pb");
    define("DB_NAME","elmqvist_org_db_volupinusminima");
}
?>