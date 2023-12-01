<?php
define("APP_BASE_DIR",dirname(__FILE__)."/../");
ini_set('default_charset', 'utf-8');
header('Content-type: text/html; charset=utf-8');
define("APP","APP");
require APP_BASE_DIR."includes.php";
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "-----------------------------------------------------<br />";
echo "-OBJECT CREATION START"."<br />";
$dbMgr = new DBManager();
$usrMgr = new UserManager($dbMgr);
$attendManager = new AttendManager($dbMgr, $usrMgr);
echo "-Authenticated:".$usrMgr->isAuthenticated()."<br />";
echo "-OK"."<br />";

$str_timestamp = "2021-03-06 12:01:00";
echo $str_timestamp."<br />";
$beautifulDate = $attendManager->getBeautifulDateSE($str_timestamp);
$time = $attendManager->getTime($str_timestamp);
echo $beautifulDate.", klockan ".$time."<br />";
$str_osatimestamp = $attendManager->getOSA($str_timestamp);
echo $str_osatimestamp."<br />";
$beautifulOSADate = $attendManager->getBeautifulDateSE($str_osatimestamp);
echo $beautifulOSADate."<br />";

echo "-----------------------------------------------------<br />";
echo "-TEST usrMgr->get_userId()"."<br />";
echo "-User:".$attendManager->get_userId()."<br />";
echo "-OK"."<br />";

echo "-----------------------------------------------------<br />";
echo "-TEST attendManager->getEvents"."<br />";
$arrEvents = $attendManager->getEvents();
echo "-Num events:".count($arrEvents)."<br />";
echo "-Listing events:"."<br />";
foreach($arrEvents as $event){
    $keys = array_keys($event);
    foreach($keys as $key){
        echo "--key:".$key." val:".$event[$key]."<br />";   
    }
}
echo "-OK"."<br />";

echo "-----------------------------------------------------<br />";
echo "-TEST attendManager->createEvent"."<br />";
$eventName = "myEventName";
$eventLocation = "myEventLocation";
$eventDate = "2020-01-01";
$eventHourMinute = "12:01";
$arrEvents = $attendManager->getEvents();
echo "-Num events:".count($arrEvents)."<br />";
$eventId = $attendManager->createEvent($eventName."1", $eventLocation, $eventDate, $eventHourMinute);
$eventId = $attendManager->createEvent($eventName."2", $eventLocation, $eventDate, $eventHourMinute);

$arrEvents = $attendManager->getEvents();
echo "-Num events:".count($arrEvents)."<br />";
echo "-OK"."<br />";

echo "-----------------------------------------------------<br />";
echo "-TEST attendManager->deleteEvent"."<br />";
$arrEvents = $attendManager->getEvents();
echo "-Num events:".count($arrEvents)."<br />";

$eventId = $eventId-1;
$attendManager->deleteEvent($eventId);

$arrEvents = $attendManager->getEvents();
echo "-Num events:".count($arrEvents)."<br />";
echo "-OK"."<br />";


echo "-----------------------------------------------------<br />";
echo "-TEST attendManager->getAnswer"."<br />";
$eventId = $eventId+1;
$answer = $attendManager->getAnswer($eventId);
if($answer){
    $answer = "YES";
}
else{
    $answer = "NO";   
}
echo "-Is attending:".$answer."<br />";
echo "-OK"."<br />";

echo "-----------------------------------------------------<br />";
echo "-TEST attendManager->setAnswer = TRUE"."<br />";
$attendManager->setAnswer($eventId, TRUE);
$answer = $attendManager->getAnswer($eventId);
if($answer){
    $answer = "YES";
}
else{
    $answer = "NO";   
}
echo "-Is attending:".$answer."<br />";
echo "-OK"."<br />";

echo "-----------------------------------------------------<br />";
echo "-TEST attendManager->setAnswer = FALSE"."<br />";
$attendManager->setAnswer($eventId, FALSE);
$answer = $attendManager->getAnswer($eventId);
if($answer){
    $answer = "YES";
}
else{
    $answer = "NO";   
}
echo "-Is attending:".$answer."<br />";
echo "-OK"."<br />";


?>
