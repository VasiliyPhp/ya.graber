<?php
namespace app\common;

class YandexMessageProvider{
	
	private $key = '6585ur845423468yopohcdwr54ghrreott56rotxyp95ott';
	private $pref;
	private $suf;
	private $cPref;
	private $cSuf;
	private $body;
	private $subject;
	private $currentIndex = 0;
	private static $maxFailureCount = 10;
	private static $failureCount = 0;
	
	function __construct ( $prefFile, $sufFile, $messBody ) {
		$this->messageBody = $messBody;
		$this->pref = $this->load($prefFile);
		$this->cPref = $this->pref ? 
		  count($this->pref) :
			null;
		$this->suf = $this->load($sufFile);
		$this->cSuf = $this->suf ? 
		  count($this->suf) :
			null;
		$this->currentIndex = mt_rand(0, $this->cPref * $this->cSuf );
	}
	
	function next () {
		$index = &$this->currentIndex;
		
		$suf = ($this->cSuf  + $index ) % $this->cSuf;
		
		$pref = ((int) ($index / $this->cSuf)) % $this->cPref;
		
		$subject = $query = $this->pref[$pref] . ' ' . $this->suf[$suf];
		
		$next = $this->api($query);
		
		if(!$next){
			return $this->next();
		}
		
		$body = strip_tags(str_replace('@', '', $this->api($query)));
    // exit($body);
		$body = '<!doctype html><html><head></head><body lang="Ru_ru">'
			    . '<div style="text-ident:-999px;font-size:3px;color:#fff">'.substr($body, strlen($body)/10 , strlen($body)/10 + 50 ) . '</div>'
		      . $this->messageBody
			    . '<div style="font-size:2px;color:#fff">'.substr($body, 0, strlen($body)/10) . '</div>';
		$index++;
		
		$result = compact('body','subject');
		
		return $result;
	}
	
	function api($query){
    $url = sprintf('http://localhost/yh?key=%s&query=%s', $this->key, rawurlencode($query));
    $tmp = trim(file_get_contents($url));
		$response = json_decode($tmp);
		if(isset($response->error)){
			if($response->error->msg == 'not_found'){
				return null;
			}
			if(++self::$failureCount >= self::$maxFailureCount){
			  throw new \Exception('Не работает яндекс апи. Ответ - ' . $response->error->msg ."\n" . $response->error->resp);
			}
			return $this->api($query);
  	}
		self::$failureCount = 0;
		return $response->response->resp;
	}
	
	function load($file){
		return file($file, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
	}
}









