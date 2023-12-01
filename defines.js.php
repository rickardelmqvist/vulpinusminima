<?php
header("Content-Type: application/javascript");
define("APP_BASE_DIR",dirname(__FILE__)."/");
define("APP","APP");
include APP_BASE_DIR."/includes.php"; 
session_start();
$dbMgr = new DBManager();
$usrMgr = new UserManager($dbMgr);
$authenticated = $usrMgr->isAuthenticated();
    
?>
g_api_server = "<?php echo(VIEWCONTROLLER); ?>";
g_inside_server = "<?php echo(INSIDECONTROLLER); ?>";

g_session = "<?php echo(session_id()); ?>";
g_loginstate = "<?php echo($usrMgr->loginState()); ?>"; 

g_savebtn_dim = 30;
g_modifier_dim = 60;
g_speed = 500;
