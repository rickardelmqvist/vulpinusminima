<?php
    ini_set('default_charset', 'utf-8');
    header('Content-type: text/html; charset=utf-8');
    define("APP_BASE_DIR",dirname(__FILE__)."/");
    define("APP","APP");
    require "includes.php";
    
    session_start();

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);




    $dbMgr = new DBManager();
    $usrMgr = new UserManager($dbMgr);
    //$usrMgr->doLogout();
    $authenticated = $usrMgr->isAuthenticated();


    include "head.php";   
?>
<div class="view inside"><?php include "view_inside.php"; ?></div>
<div class="view user">user</div>
<div class="view resetpw"><?php include "view_resetpw.php"; ?></div>
<div class="view register"><?php include "view_register.php"; ?></div>
<div class="view login"><?php include "view_login.php"; ?></div>
<div class="view authenticate"><?php include "view_authenticate.php"; ?></div>
<div id="blocker"></div>
<div id="popup">
    <div class="popup_workarea">
        <div class="title" id="popup_header"></div>
        <div id="popup_textarea"></div>
        <div class="div_button" id="do_popup_ok">Ok</div>
        <div class="div_button" id="do_popup_cancel">Cancel</div>
    </div>
</div>

<?php 
    include "foot.php"; 
?>