<?php
namespace app\common;

class HtmlMessageProvider{
	
	private $templateSubject;
	private $templateBody;
	private $cTemplateBody;
	private $cTemplateSubject;
	private $body;
	private $subject;
	private $currentIndex = 0;
	
	function __construct ( $subj, $body ) {
		$this->body = $body;
		$this->subject = $subj;
		$this->templateBody = $this->parseTemplate($this->body);
		$this->cTemplateBody = $this->templateBody ? 
		  count($this->templateBody) :
			null;
		$this->templateSubject = $this->parseTemplate($this->subject);
		$this->cTemplateSubject = $this->templateSubject ? 
		  count($this->templateSubject) :
			null;
	}
	function get_random($matches){
		$matches = explode('|', $matches[1]);
		return $matches[array_rand($matches)];
	}
	function next () {
		$index = &$this->currentIndex;
		if($this->cTemplateBody){
		  $bIndex = ($this->cTemplateBody + $index) % $this->cTemplateBody;
	    $body = preg_replace_callback('~{(.+)}~U', [$this, 'get_random'], $this->body);
		} else{
	    $body = $this->body;
		}
		if($this->cTemplateSubject){
	    $sIndex = ($this->cTemplateSubject + $index) % $this->cTemplateSubject;
		  $subject = preg_replace_callback('~{(.+)}~U', [$this, 'get_random'], $this->subject);
		} else {
			$subject = $this->subject;
		}
		$index++;
		$result = compact('body','subject');
		return $result;
	}
	
	function parseTemplate($string){

		$result = preg_match('~\{(.+)\}~uU', $string, $tmp) ? 
		  explode('|', $tmp[1]) :
			null;
		return $result;
	}
}