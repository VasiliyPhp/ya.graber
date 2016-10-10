<?php

/**
	*
	* @My special lib for site parsing
	*
	* @author: Wasiliy Gerlah Gerlakh
	*
	*/
	
namespace app\common;

class Parser{
	public static $emailPattern = '~\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}\b~';
	private static $mobileUserAgents = ['PHILIPS9A9W ObigoInternetBrowser/QO3C Profile/MIDP-2.0 Configuration/CLDC-1.1',
				 'RoverPC-S5/Ver1.0.1/WAP2.0 Profile/MIDP-2.0 Configuration/CLDC-1.1/ (compatible; MSIE 4.01; Windows CE; PPC; 240x320)',
				 'SAMSUNG-SGH-G600/G600XEHE1 NetFront/3.4 Profile/MIDP-2.0 Configuration/CLDC-1.1',
				 'SIE-C75/11 UP.Browser/7.0 MMP/2.0 Profile/MIDP-2.0 Configuration/CLDC-1.1',
				 'SonyEricssonT700/R3EA Browser/NetFront/3.4 Profile/MIDP-2.1 Configuration/CLDC-1.1 JavaPlatform/JP-8.3.3',
				 'SonyEricssonC702/R3CA Browser/NetFront/3.4 Profile/MIDP-2.1 Configuration/CLDC-1.1 JavaPlatform/JP-8.3.1',
				 'Opera/9.6 (J2ME/MIDP; Opera Mini/4.3 U; ru)',
				 'Noki680/1.0 SymbianOS/8.0 Series60/2.6 Profile/MIDP-2.0 Configuration/CLDC-1.1/UC Browser7.0.0.41/27/400',
				 'Noki200/2.0 Profile/MIDP-2.0 Configuration/CLDC-1.1', 
				 'SAMSUNG-SGH-F480/F48FXEIB1 SHP/VPP/R5 NetFront/3.4 Qtv5.3 SMM-MMS/1.2 profile/MIDP-2.0 configuration/CLDC-1.1'];
	static function getMobileUserAgent(){
		 return self::$mobileUserAgents[mt_rand(0, count(self::$mobileUserAgents)-1)];
	}
	 
	static function changeMobileUserAgent($ua){
		$ua = array_diff(self::$mobileUserAgents,[$ua]);
		// exit(print_r($ua));
		// echo count($ua);
		return $ua[array_rand($ua)];
	}
	 
	static function changeIp(){
		return mt_rand(1,239).'.'.mt_rand(1,254).'.'.mt_rand(1,254).'.'.mt_rand(1,254);
	}
 
  static function query($host, $data = false, $path = '/',
											 $referer = false, $cookie = null, $ip = null,
											 $ua = null, $is_xrv = false, $show_headers = false){
		$user_agent = $ua ? : "Mozilla/5.0 (Windows NT 6.3; WOW64; rv:45.0) Gecko/20100101 Firefox/45.0";
		$port = '80';
		$last_ip = $ip ? : false;
		$fp = @fsockopen($host,$port,$errno,$ermsg, 10);
		if(!$fp){
			return false;
		}
		$method = $data?"POST":"GET";
		$query = "$method $path HTTP/1.0\r\n";
		$query.= "Host: {$host}\r\n";
		$query.= ($user_agent)?"User-Agent: {$user_agent}\r\n":null;
		$query.= ($last_ip)?"X-REAL-IP: ".$last_ip."\r\n":null;
		$query.= ($referer)?"Referer: {$referer}\r\n":null;
		$query.= "Accept: */*\r\n";
		$query.= $data?"Content-Length: ".strlen($data)."\r\n":"";
		$query.= $data?"Content-type: application/x-www-form-urlencoded; charset=UTF-8\r\n":"";
		$query.= ($is_xrv)?"X-Requested-With: XMLHttpRequest\r\n":"";
		$query.= ($cookie)?"Cookie: {$cookie}\r\n":null;
		$query.= "Connection: close\r\n\r\n";
		$query.= $data?$data."\r\n\r\n":'';

		fwrite($fp, $query);

		$result = '';

		while(!feof($fp)){
			$result.=fgets($fp,1024);
		}

		$response_headers = strstr($result,"\r\n\r\n", true);
		$responce_body = trim(strstr($result,"\r\n\r\n"));
		echo ($show_headers)?"<pre><b>sended headers:</b>\n$query\n<b>responsed headers:</b>\n".$response_headers."\n</pre><hr>":null;
		$http_code = preg_match('/HTTP\/1\.1\s*(\d{3}).*/iU', strstr($response_headers, "\r\n", true), $out) ? $out[1] : null;
		
		return array($response_headers, $responce_body, $http_code);

	}
}