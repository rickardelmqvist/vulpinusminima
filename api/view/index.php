<?php
define("APP_BASE_DIR",dirname(__FILE__)."/../../");
ini_set('default_charset', 'utf-8');
header('Content-type: text/html; charset=utf-8');
define("APP","APP");
require APP_BASE_DIR."includes.php";
session_start();
$dbMgr = new DBManager();

$usrMgr = new UserManager($dbMgr);
$usrMgr->isAuthenticated();

$myObj->request = $_POST['request'];
$myObj->g_session = $_POST['g_session'];
$myObj->result = "faliure";



switch ($myObj->request) {
    case 'get_salt':
        $myObj->salt = rand(1,5);
        $_SESSION['salt'] = $myObj->salt;
        break;
        
    case 'do_register':
        
        $g_session = $_POST['g_session'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $name = $_POST['name'];
        $cleartextpw = $_POST['cleartextpw'];
        $md5_password = $_POST['md5_password'];
        $salt = $_SESSION['salt'];
        $_SESSION['salt'] = NULL;
        
        $result = $usrMgr->registerUser($g_session, $email, $name, $salt, $password,$cleartextpw, $md5_password);
        if($result){
            $myObj->result = "success";
        }
        else{
            $myObj->last_error = $usrMgr->get_last_error();
        }
        break;
    
    case 'do_authenticate':
        $token = $_POST['token'];
        
        $result = $usrMgr->authenticateUser($token);
        if($result){
            $myObj->result = "success";
        }
        else{
            $myObj->last_error = $usrMgr->get_last_error();
        }
        break;
        
    case 'do_request_reset':
        $email = $_POST['email'];
        $result = $usrMgr->resetPassword($email);
        $myObj->email = $email;
        if($result){
            $myObj->result = "success";
        }
        else{
            $myObj->last_error = $usrMgr->get_last_error();
        }
        break;
        break;
        
    case 'do_logout':
        $usrMgr->doLogout();
        $myObj->result = "success";
        $myObj->g_session = session_id();
        break;
        
    case 'do_start_login':
        $_SESSION['salt'] = NULL;
        $email = $_POST['email'];

        $g_session = $_POST['g_session'];
        $md5_password = $_POST['md5_password'];
        $sess_encoded = $_POST['sess_encoded'];
        $login_password = $_POST['login_password'];
        
        $result = $usrMgr->doStartLogin($email,$g_session,$sess_encoded, $md5_password, $login_password);

        if($result){
            $myObj->result = "success";
        }
        else{
            $myObj->last_error = $usrMgr->get_last_error();
        }
        break;
        
    case 'get_slide_menu':
        $myObj->result = "success";
        $myObj->name = $usrMgr->get_name();
        break;
    
}
$usrMgr->isAuthenticated();
$myObj->loginState = $usrMgr->loginState();
$myJSON = json_encode($myObj);
echo $myJSON;

?>

