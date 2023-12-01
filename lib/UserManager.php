<?php
ini_set('default_charset', 'utf-8');
define("UM_ERROR_EMAIL_EXISTS",1);
define("UM_ERROR_CANNOT_FIND_USER_ID",2);
define("UM_AUTHENTICATE_FAIL_NO_USER",3);
define("UM_ERROR_INVALID_PASS_KEY",4);

define("UM_LOGINSTATE_UNKNOWN","UM_LOGINSTATE_UNKNOWN");
define("UM_LOGINSTATE_UNREGISTERED","UM_LOGINSTATE_UNREGISTERED");
define("UM_LOGINSTATE_AUTHENTICATED","UM_LOGINSTATE_AUTHENTICATED");
define("UM_LOGINSTATE_UN_AUTHENTICATED","UM_LOGINSTATE_UN_AUTHENTICATED");
define("UM_LOGINSTATE_UN_AUTHENTICATED_RETURNING","UM_LOGINSTATE_UN_AUTHENTICATED_RETURNING");
define("UM_LOGINSTATE_UN_AUTHENTICATED_NEW","UM_LOGINSTATE_UN_AUTHENTICATED_NEW");


class UserManager{
    private $dbManager;
    private $last_error;
    private $login_state = UM_LOGINSTATE_UNKNOWN;
    
    private $userId;
    private $name;
    private $email;
    
    function __construct($dbManager) {
         $this->dbManager = $dbManager;
    }
    
    function get_userId(){
        return $this->userId;
    }
    function get_email(){
        return $this->email;
    }
    function get_name(){
        return $this->name;
    }
    
    function get_last_error() {
        return $this->last_error;
    }
    
    function _registerLogin($userId){
        
        $sql = "DELETE FROM t_login WHERE userId='".$userId."'";
        $this->dbManager->query($sql);
        
        $sql = "DELETE FROM t_login WHERE sessionId='".session_id()."'";
        $this->dbManager->query($sql);
        
        $sql = "INSERT INTO t_login ";
        $sql.="(userId, passKey, authenticated, sessionId) ";
        $sql.="VALUES ";
        $sql.="('".$userId."','".rand (10000,99999)."',FALSE,'".session_id()."')";
        $this->dbManager->query($sql);
        
        $this->_sendmail($userId);
    }
    
    function _sendmail($userId){
        
        $sql = "SELECT * FROM t_user WHERE userId='".$userId."'";
        $this->dbManager->query($sql);
        
        if( $this->dbManager->get_num_rows() == 0){
            $this->last_error = UM_ERROR_CANNOT_FIND_USER_ID;
            return FALSE;
        }
        
        $r = $this->dbManager->fetch_assoc();
        
        $to = $r['email'];

        $name = $r["name"];
        
        $sql = "SELECT * FROM t_login WHERE userId='".$userId."'";
        $this->dbManager->query($sql);
        $r = $this->dbManager->fetch_assoc();
        
        $passKey = $r["passKey"];
        
        $subject = 'Vulpinus Minima - Inloggningskod';
      
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers = "From: mini@vulpinusminima.se" . "\r\n";

        $body ="Hej, ".$name."\r\n";
        $body.= "Fyll i koden på inloggningssidan.\r\n";
        $body.="Inloggningskod: ".$passKey."\r\n";
        $body.="Koden är giltig i fem minuter.\r\n";
        
        $subject = mb_encode_mimeheader($subject, 'UTF-8', 'B');
        $body = utf8_decode($body);
        mail($to,$subject,$body,$headers);

        return TRUE;
    }
    
    function _sendpassmail($name,$email,$password){

        $to = $email;
        
        $subject = 'Vulpinus Minima - Nytt lösenord';
      
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers = "From: mini@vulpinusminima.se" . "\r\n";

        $body ="Hej, ".$name."\r\n";
        $body.= "Här är ditt nya lösenord.\r\n";
        $body.="Lösenord: ".$password."\r\n";
        $body.="Du måste logga in inom fem minuter.\r\n";
        
        $subject = mb_encode_mimeheader($subject, 'UTF-8', 'B');
        $body = utf8_decode($body);
        mail($to,$subject,$body,$headers);
        
        return TRUE;
    }
    
    
    function registerUser($g_session, $email, $name, $salt, $password,$cleartextpw, $md5_password){
        

        $decrypted = "";
        for( $i = 0; $i < strlen($password); $i++ ) {
            $b = ord($password[$i]);
            $a = $b ^ $salt;  // <-- must be same number used to encode the character
            $decrypted .= chr($a);
        }
        
        
        
        
        
        
        
        $md5Encoded = str_replace(session_id().".","",$decrypted);
        
        //echo "g_session:".$g_session."*";
        //echo "session_id:".session_id()."*";
        
        //echo $email."*";
        //echo $name."*";
        //echo "salt:".$salt."*";
        //echo $password."*";
        //echo $cleartextpw."*";
        //echo $md5_password."*";
        //echo "decrypted:".$decrypted."*";
        //echo "md5Encoded:".$md5Encoded."*";
        //echo "MD5_PV:".md5($cleartextpw)." ";
        
        $sql = "SELECT * FROM t_user WHERE email='".$email."'";
        $this->dbManager->query($sql);
        
        if( $this->dbManager->get_num_rows() > 0){
            $this->last_error = UM_ERROR_EMAIL_EXISTS;
            return FALSE;
        }

        $sql = "INSERT INTO t_user (email, name) VALUES ('".$email."', '".$name."')";
        $this->dbManager->query($sql);
        $userId =  $this->dbManager->get_last_id();
        
        $sql = "INSERT INTO t_password (userId, md5Value) VALUES ('".$userId ."','".$md5Encoded."')";
        $this->dbManager->query($sql);
        
        $this->_registerLogin($userId);
        
        return TRUE;  
    } 
    function doStartLogin($email,$g_session, $sess_encoded = NULL, $md5_password = NULL, $login_password = NULL){
        
        //echo "HERE:";
        //echo "session:".$g_session." ";
        //echo "MY_SESSION:".session_id()." ";
        
        //echo "EMAIL:".$email." ";
        
        //echo "login_password:".$login_password." ";
        
        //echo "md5_password:".$md5_password." ";
        //echo "MD5_PV:".md5($login_password)." ";
        

        
        //echo "sess_encoded:".$sess_encoded." ";
        //echo "MY SESS_ENCODED 1:".md5(session_id().md5($login_password))." ";
        //echo "MY SESS_ENCODED 2:".md5(session_id().$md5_password)." ";
       
        
       
        
        
        $sql = "SELECT * FROM t_user WHERE email='".$email."'";
      
      	//echo $sql;
        
        $this->dbManager->query($sql);
        if( $this->dbManager->get_num_rows() == 0){
            $this->last_error = UM_ERROR_CANNOT_FIND_USER_ID;
            //echo "GOING HERE";
            return FALSE;
        }
        else
        {
            $r = $this->dbManager->fetch_assoc();
            $userId = $r['userId'];
            
            
            $sql = "SELECT * FROM t_password WHERE userId='".$userId."'";
            $this->dbManager->query($sql);
            if( $this->dbManager->get_num_rows() == 1){
                $r = $this->dbManager->fetch_assoc();
                $md5Value = $r['md5Value'];
                
                //echo "md5_password in db ".$md5Value."* ";
                //echo "md5_password:".$md5_password." ";
                //echo "MD5_PV:".md5($login_password)." ";
                //exit();
                
                //echo "*".md5(session_id().$md5Value)."*";
                //echo $sess_encoded."*";
                
                if( md5(session_id().$md5Value) == $sess_encoded)
                {
                    //success.....
                    $this->_registerLogin($userId);
                    return TRUE;
                }
            }
        }
        return FALSE;
    }
    function doLogout()
    {
        session_regenerate_id();
    }
    
    function resetPassword($email){
        
        $sql = "SELECT * FROM t_user WHERE email='".$email."'";
        $this->dbManager->query($sql);
        if( $this->dbManager->get_num_rows() == 0){
            $this->last_error = UM_ERROR_EMAIL_EXISTS;
            return TRUE;
        }
        $r= $this->dbManager->fetch_assoc();
        $userId = $r['userId'];
        $name = $r['name'];
        
        $password = "!small_foxes_and_chocolate!".rand(1,1000);
        $md5Value = md5($password);
        $sql = "UPDATE t_password SET md5Value='".$md5Value."' WHERE userId='".$userId."'"; 
        $this->dbManager->query($sql);
        $this->_sendpassmail($name,$email, $password);
        return TRUE;
    }
    
    function authenticateUser($token){
        
        $sql = "SELECT * FROM t_login WHERE sessionid='".session_id()."'";
        $this->dbManager->query($sql);
        if( $this->dbManager->get_num_rows() == 0){
            $this->last_error = UM_AUTHENTICATE_FAIL_NO_USER;
            return FALSE;
        }
        $r = $this->dbManager->fetch_assoc();
        
        $actualPassKey = $r['passKey'];
        $userId = $r['userId'];
        if( $actualPassKey == $token)
        {
            $sql = "UPDATE t_user SET isVerified=TRUE WHERE userId='".$userId."'"; 
            $this->dbManager->query($sql);
            
            $sql = "UPDATE t_login SET authenticated=TRUE WHERE sessionId='".session_id()."'"; 
            $this->dbManager->query($sql);
            
            return TRUE;
        }
        else{
            $this->last_error = UM_ERROR_INVALID_PASS_KEY;
            return FALSE;
        }
        return FALSE;
    }
    function isAuthenticated() {
        
        if($this->login_state = UM_LOGINSTATE_AUTHENTICATED && $this->userId != NULL){
            return TRUE;
        }
        
        $this->login_state = UM_LOGINSTATE_UNKNOWN;
        
        $sql = "SELECT * FROM t_login WHERE sessionid='".session_id()."'";
        $this->dbManager->query($sql);
        if( $this->dbManager->get_num_rows() == 0){
            $this->login_state = UM_LOGINSTATE_UNREGISTERED;
        }
        else{
            $r = $this->dbManager->fetch_assoc();
            $userId = $r['userId'];
            $authenticated = $r['authenticated'];
            
            if($authenticated){
                $this->login_state = UM_LOGINSTATE_AUTHENTICATED;
                $this->userId = $userId;
                
                $sql = "SELECT * FROM t_user WHERE userId='".$userId."'"; 
                $this->dbManager->query($sql);
                $r = $this->dbManager->fetch_assoc();
                $this->name = $r['name'];
                $this->email = $r['email'];
            }
            else{
                
                $this->login_state = UM_LOGINSTATE_UN_AUTHENTICATED;
                
                $sql = "SELECT * FROM t_user WHERE userId='".$userId."'"; 
                $this->dbManager->query($sql);
                if( $this->dbManager->get_num_rows() == 0){
                    $this->login_state = UM_LOGINSTATE_UNREGISTERED;
                }
                else{
                    $r = $this->dbManager->fetch_assoc();

                    $isVerified = $r['isVerified'];
                    if($isVerified){
                        $this->login_state = UM_LOGINSTATE_UN_AUTHENTICATED_RETURNING;
                    }
                    else{
                         $this->login_state = UM_LOGINSTATE_UN_AUTHENTICATED_NEW;
                    } 
                }

            }
        }
        return ($this->login_state == UM_LOGINSTATE_AUTHENTICATED);
    }
    
    function loginState(){
        return $this->login_state;
    }
    
    
        
}
?>
