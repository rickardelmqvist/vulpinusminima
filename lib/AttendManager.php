<?php
ini_set('default_charset', 'utf-8');
define("EM_ERROR_CANNOT_FIND_EVENT",1);

class AttendManager{
    private $dbManager;
    private $userManager;
    private $arrEvents;

    
    function __construct($dbManager, $userManager) {
        $this->dbManager = $dbManager;
        $this->userManager = $userManager;   
        $this->userManager = $userManager;   
    }
    
    function get_userId(){
        return $this->userManager->get_userId();
    }
    
    function getEvents(){
        $sql = "SELECT * FROM t_event ORDER BY eventTimestamp DESC";
        $this->dbManager->query($sql);
        $arr = array();
        if( $this->dbManager->get_num_rows() > 0){
            while($r = $this->dbManager->fetch_assoc()){
                $arr[] = $r;
            }
        }
        return $arr; 
    }
    function getLastEvent(){
        $sql = "SELECT * FROM t_event ORDER BY eventTimestamp DESC";
        $this->dbManager->query($sql);
        $arr = array();
        if( $this->dbManager->get_num_rows() > 0){
            $r = $this->dbManager->fetch_assoc();
            return $r;    
        }
        return NULL;
    }
    
    function setInvitation($eventId,$userId){
        if($userId == NULL){
            return;
        }
        $sql = "SELECT * FROM t_attend WHERE eventId='".$eventId."' AND userId='".$userId."'";
        $this->dbManager->query($sql);
        $r = $this->dbManager->fetch_assoc();
        if( $this->dbManager->get_num_rows() == 0){
            $sql = "INSERT INTO t_attend (userId, eventId) VALUES (".$userId.",".$eventId.")";
            $this->dbManager->query($sql);
        }
    }
    
    
    function createEvent($eventName, $eventLocation, $eventDate, $eventHourMinute){
        $eventTimestamp = $eventDate." ".$eventHourMinute.":00";
        $sql = "
        INSERT INTO t_event (
            eventName
        ,   eventLocation
        ,   eventTimestamp
        ,   openForRegister
        ) 
        VALUES (
            '".$eventName."'
        ,   '".$eventLocation."'
        ,   '".$eventTimestamp."'
        ,   TRUE
        )";
        $this->dbManager->query($sql);
        $eventId = $this->dbManager->get_last_id();
        
        $sql = "SELECT userId FROM t_user";
        $this->dbManager->query($sql);
        $arr_sql = array();
        while($r = $this->dbManager->fetch_assoc()){
            $arr_sql[] = "INSERT INTO t_attend (userId, eventId) VALUES (".$r['userId'].",".$eventId.")";
        }
        foreach($arr_sql as $sql){
            $this->dbManager->query($sql);
        }
        return $eventId;
    }
    function deleteEvent($eventId){
        $sql = "DELETE FROM t_event WHERE eventId='".$eventId."'";
        $this->dbManager->query($sql);
        $sql = "DELETE FROM t_attend WHERE eventId='".$eventId."'";
        $this->dbManager->query($sql);
        
    }
    
    function getHasAnswered($eventId){
        $sql = "SELECT * FROM t_attend WHERE eventId='".$eventId."' AND userId='".$this->get_userId()."'";
        $this->dbManager->query($sql);
        $r = $this->dbManager->fetch_assoc();
        if($r['hasAnswered']){
            return TRUE;
        }
        return FALSE;
    }
    
    function getAnswer($eventId){
        $this->setInvitation($eventId,$this->get_userId());
        $sql = "SELECT * FROM t_attend WHERE eventId='".$eventId."' AND userId='".$this->get_userId()."'";
        $this->dbManager->query($sql);
        $r = $this->dbManager->fetch_assoc();
        if($r['answer']){
            return TRUE;
        }
        return FALSE;
    }
    
    function setAnswer($eventId, $answer){ 
        $this->setInvitation($eventId,$this->get_userId());
        
        $str = "FALSE";
        if($answer){
             $str = "TRUE";
        }
        
        $sql = "UPDATE t_attend SET hasAnswered=TRUE, answer=".$str." WHERE userId='".$this->get_userId()."' AND eventId='".$eventId."'";
        
        //echo $sql;
        $this->dbManager->query($sql);
        $this->_sendConfirmail($eventId);
        $this->_sendListRegistered($eventId);
        
    }

    function _sendConfirmail($eventId){
        
        $userId = $this->get_userId();
        $sql = "SELECT 
            t_event.eventId
        ,	t_attend.userId
        ,	t_event.eventName
        ,	t_event.eventTimestamp
        ,	t_event.eventLocation
        ,   t_attend.hasAnswered
        ,   t_attend.answer
        ,   t_attend.didAttend
        FROM 
            t_event 
        LEFT JOIN
            t_attend
        ON
            t_event.eventId = t_attend.eventId
        WHERE t_attend.userId = ".$userId."
        AND t_event.eventId = ".$eventId;
        
        $this->dbManager->query($sql);
        
        if( $this->dbManager->get_num_rows() == 0){
            $this->last_error = EM_ERROR_CANNOT_FIND_EVENT;
            return FALSE;
        }
        
        $r = $this->dbManager->fetch_assoc();
        
        $r["answer"];
        $to = $this->userManager->get_email();
        $name = $this->userManager->get_name();
        
        $subject = 'Vulpinus Minima - Bekräftelse';
      
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers = "From: mini@vulpinusminima.se" . "\r\n";

        $body ="Hej, ".$name."\r\n";
        $body.= "Tack för ditt svar.\r\n";
        $body.= "Du har svarat att du ";
        if($r["answer"] == FALSE){ 
             $body.= "inte ";
        }
        $body.= "kommer att delta på ";
        $body.= utf8_encode($r["eventName"])." ".$r["eventTimestamp"].".";
        
        
        $subject = mb_encode_mimeheader($subject, 'UTF-8', 'B');
        $body = utf8_decode($body);
        mail($to,$subject,$body,$headers);

        return TRUE;
    }
    function getOSA($timestamp,$numdaysadvance = 14){
        $year = intval(substr($timestamp,0,4));
        $month = intval(substr($timestamp,5,2));
        $day = intval(substr($timestamp,8,2));
        return date('Y-m-d 00:00:00', strtotime( $year."-".$month."-".$day. ' - '.$numdaysadvance.' days'));
    }
    function getBeautifulDateSE($timestamp){
        $year = intval(substr($timestamp,0,4));
        $month = intval(substr($timestamp,5,2));
        $day = intval(substr($timestamp,8,2));
        $hourminute = substr($timestamp,11,5);
        $dayofweek = date("w", mktime(0, 0, 0, $month, $day, $year));
        $days = ["måndag","tisdag","onsdag","torsdag","fredag","lördag","söndag"];
        $months = ["januari","februari","mars","april","maj","juni","juli","augusti","september","oktober","november","december"];
        
        $prettydate = $days[$dayofweek-1]."en den ".$day." ".$months[$month-1];
        return $prettydate;
    }
    function getTime($timestamp){
        $hourminute = substr($timestamp,11,5);
        return $hourminute;
    }
    
    function _sendListRegistered($eventId){
        
        $to = "rickard@elmqvist.org";
        $to2 = "magnus@winter.se";
        
        $subject = 'Vulpinus Minima - Anmälda';
      
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers = "From: mini@vulpinusminima.se" . "\r\n";
        
        
        $sql = "
        SELECT 
            t_event.eventId
        ,	t_attend.userId
        ,	t_user.name
        ,	t_event.eventName
        ,	t_event.eventTimestamp
        ,	t_event.eventLocation
        ,   t_attend.hasAnswered
        ,   t_attend.answer
        ,   t_attend.didAttend
        FROM 
            t_event
        LEFT JOIN
            t_attend
        ON
            t_event.eventId = t_attend.eventId
        LEFT JOIN
            t_user
        ON
            t_attend.userId = t_user.userId
        WHERE t_event.eventId = ".$eventId;
        
        $this->dbManager->query($sql);
        
        $body = "Name\t\t\t\tAnswered\t\tAnswer\t\tAttended"."\r\n";
        
        while($r = $this->dbManager->fetch_assoc()){
            $hasAnswered = $r['hasAnswered'];
            $answer = $r['answer'];
            $didAttend = $r['didAttend'];
            
            $body .= $r['name']."\t\t\t\t".$hasAnswered."\t\t".$answer."\t\t".$didAttend."\r\n";
            
        }
        
        //echo $body;

        $subject = mb_encode_mimeheader($subject, 'UTF-8', 'B');
        $body = utf8_decode($body);
        mail($to,$subject,$body,$headers);
        mail($to2,$subject,$body,$headers);

        return TRUE;
    }      
}
?>
