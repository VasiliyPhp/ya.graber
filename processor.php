<?php
	
	$post = array_filter($_POST);
	extract($post);
	if (!isset($theme,$email,$subid,$date,$uid)){
		header ("HTTP/1.1 404 Not Found");
		echo "Not found";
		exit;
	}
	
	$emailBody = sprintf("
	<h2>email <strong>%s</strong> отписался</h2>
	<p>тема письма: <strong>%s</strong></p>
	<p>идентификатр рассылки: <strong>%s</strong></p>
	<p>идентификатор письма: <strong>%s</strong></p>
	<p>время начала рассылки по МСК: <strong>%s</strong></p>
	", $email, $theme, $subid, $uid, date('%Y-%m-%d %H:%i'), $date);
	
	$emailTheme = '=?utf-8?B?' . base64_decode() . '?=';
	
	$headers = "Content-type: text/html; charset=utf-8;\r\nFrom: report_unsubscribe@seo.sgm.ru";
	
	$to = 'post.proposition@yandex.ru';
	ignore_user_abort(true);    
	echo $response = "<h1>Вы отписались от рассылки</h1>";
	
	
	$size = ob_get_length();    
    
	header("Content-Length: $size"); 
    header('Content-Encoding: none');    
    header('Connection: close');    
	ob_end_flush(); 
    ob_flush(); 
    flush(); 
	mail($to, $emailTheme, $emailBody, $headers);
	
	