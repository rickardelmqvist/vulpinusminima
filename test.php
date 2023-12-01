<?php
$name = "Rickard";
$password = "[PASS]";
$to = "elmqvistrickard@gmail.com";

$subject = 'Vulpinus Minima - Nytt lösenord';
$subject = mb_encode_mimeheader($subject, 'UTF-8', 'B');

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers = "From: mini@vulpinusminima.se" . "\r\n";

$body ="Hi,".$name."\r\n";
$body.= "Här är ditt nya lösenord.\r\n";
$body.="Password:".$password."\r\n";
$body.="Du måste logga in inom fem minuter\r\n";
$body = utf8_decode($body);


mail($to,$subject,$body,$headers);
?>