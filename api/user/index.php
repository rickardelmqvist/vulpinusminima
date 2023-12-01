<?php
define("APP_BASE_DIR",dirname(__FILE__)."/../../");
ini_set('default_charset', 'utf-8');
header('Content-type: text/html; charset=utf-8');
define("APP","APP");
require APP_BASE_DIR."includes.php";
session_start();
$dbMgr = new DBManager();

$usrMgr = new UserManager($dbMgr);

//$usrMgr->doLogout();

$usrMgr->isAuthenticated();

$gameMgr = new TickGameManager($dbMgr, $usrMgr);


$myObj->request = $_POST['request'];
$myObj->result = "faliure";

switch ($myObj->request) {            
    case 'get_game':
        break;
}
$myObj->loginState = $usrMgr->loginState();
$myJSON = json_encode($myObj);
echo $myJSON;

?>

