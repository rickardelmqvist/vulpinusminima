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
$attendManager = new AttendManager($dbMgr, $usrMgr);


$myObj->request = $_POST['request'];
$myObj->result = "faliure";

switch ($myObj->request) {            
    case 'get_home_data':
        //heres, where we load and return...
        $myObj->result = "success";
        $template = file_get_contents("page_data/home_data.json");
        $template = str_replace("\n", $name, $template);
        $template = str_replace("\r", $name, $template);
        $name = $usrMgr->get_name();
        $template = str_replace("{name}", $name, $template);
        
        $arrEvents = $attendManager->getEvents();
        $event =  $arrEvents[0];
        
        $eventId = $event['eventId'];
        $eventName = utf8_encode($event['eventName']);
        $openForRegister = $event['openForRegister'];
        $eventTimestamp= $event['eventTimestamp'];
        $eventLocation= utf8_encode($event['eventLocation']);
        $eventDetails= utf8_encode($event['details']);
        
        $beautifulDate = $attendManager->getBeautifulDateSE($eventTimestamp);
        $time = $attendManager->getTime($eventTimestamp);
        $eventOSATimestamp = $attendManager->getOSA($eventTimestamp);
        $beautifulOSADate = $attendManager->getBeautifulDateSE($eventOSATimestamp);
        
        $hasAnswered = $attendManager->getHasAnswered($eventId);
        $answer = $attendManager->getAnswer($eventId);
        
        $myObj->eventId = $eventId;
        $myObj->eventTimestamp = $eventTimestamp;
        $myObj->eventOSATimestamp = $eventOSATimestamp;
        $myObj->beautifulDate = $beautifulDate;
        $myObj->beautifulOSADate = $beautifulOSADate;
        $myObj->eventLocation = $eventLocation ;
        $myObj->eventDetails = $eventDetails ;
        
        $myObj->hasAnswered = 0;
        if($hasAnswered){ 
            $myObj->hasAnswered = 1;
        }
        $myObj->answer = 0;
        if($answer){ 
            $myObj->answer = 1;
        }
        
        $check_yes = "";
        $check_no = "";
        if( $hasAnswered){
            if($answer){
                $check_yes = "checked";
            }
            else{
                $check_no = "checked";
            }
        }
        
        $template = str_replace("{eventDate}", ucfirst($beautifulDate.", klockan ".$time), $template);
        
        $template = str_replace("{eventLocation}", $eventLocation, $template);
        
        $template = str_replace("{eventOSA}", ucfirst($beautifulOSADate), $template);
        $template = str_replace("{check_yes}", $check_yes, $template);
        $template = str_replace("{check_no}", $check_no, $template);
        $template = str_replace("{eventDetails}", $eventDetails, $template);
        
        $myObj->content = json_decode($template, true);
        break;
        
    case 'set_answer':
        $myObj->result = "success";
        $eventId = $_POST['eventId'];
        $answer = $_POST['answer'];
        
        
        
        $myObj->post_answer = $answer;
        
        if($answer == 1){
            $answer = TRUE;
        }
        else{
            $answer = FALSE;
        }
        $myObj->answer = $answer;
        
       

        $attendManager->setAnswer($eventId,$answer);
        
        

        $event = $attendManager->getLastEvent();
        $eventName = $event['eventName'];
        $openForRegister = $event['openForRegister'];
        $eventTimestamp= $event['eventTimestamp'];
        $eventLocation= $event['eventLocation'];
        
        $beautifulDate = $attendManager->getBeautifulDateSE($eventTimestamp);
        $time = $attendManager->getTime($eventTimestamp);
        $eventOSATimestamp = $attendManager->getOSA($eventTimestamp);
        $beautifulOSADate = $attendManager->getBeautifulDateSE($eventOSATimestamp);
        
        $hasAnswered = $attendManager->getHasAnswered($eventId);
        $answer = $attendManager->getAnswer($eventId);
        
        $myObj->eventId = $eventId;
        $myObj->eventTimestamp = $eventTimestamp;
        $myObj->eventOSATimestamp = $eventOSATimestamp;
        $myObj->beautifulDate = $beautifulDate;
        $myObj->beautifulOSADate = $beautifulOSADate;
        
        $myObj->hasAnswered = 0;
        if($hasAnswered){ 
            $myObj->hasAnswered = 1;
        }
        $myObj->answer = 0;
        if($answer){ 
            $myObj->answer = 1;
        }
        
        break;
        
    case 'get_principles_data':
        $myObj->result = "success";
       
        $template = file_get_contents("page_data/principles_data.json");
        $template = str_replace("\n", $name, $template);
        $template = str_replace("\r", $name, $template);
        $myObj->content = json_decode($template, true);
        break;
        
    
}
$myObj->loginState = $usrMgr->loginState();
$myJSON = json_encode($myObj);
echo $myJSON;
?>