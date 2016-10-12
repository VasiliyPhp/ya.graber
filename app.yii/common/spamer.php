<?php
namespace app\common;
use app\common\helper;

class Spamer extends \yii\base\Object {
	private $messageProvider;
	private $emailProvider;
	private $smtpProvider;
	private $logger;
	private $body;
	private $start_time;
	private $subject;
	private $email;
	private $uId;
	public $delay = 1000000;
	public $atonce = 5;
	public $from;
	public $handlerUrl;
	public $subId;
	public $flag;
	
	function setSmtpProvider ($smtpp) {
		$this->smtpProvider = $smtpp;
		return $this;
	}
	function setMessageProvider ($mesp) {
		$this->messageProvider = $mesp;
		return $this;
	}
	function setEmailProvider ($emailp) {
		$this->emailProvider = $emailp;
		return $this;
	}
	function setlogger($logger){
		$this->logger = $logger;
		return $this;
	}
	function __construct ($ar = null) {
		parent::__construct($ar);
		$this->flag = \yii::getAlias('@runtime') . '/flag';
	}
	private function getEmailUrlParams(){
		return sprintf('?uid=%s&subid=%s&theme=%s&email=%s&date=%s',
			rawurlencode($this->uId), 
			rawurlencode($this->subId),
			rawurlencode($this->subject),
			rawurlencode($this->email),
			rawurlencode($this->start_time)
		);
	}
	private function getUnsubscribeUrl(){
		if(!$this->handlerUrl){
			return null;
		}
		return sprintf('%sunsubscribe%s',$this->handlerUrl, $this->getEmailUrlParams());
	}
	public function getUnsubscribeLink(){
		return $this->handlerUrl ? 
		       "<p><a title='отписаться от рассылки новостей' href='".$this->getUnsubscribeUrl()."'>"
					."Отписаться от рассылки новостей</a></p>" :
					null;
	}
	public function getUnsubscribeImg(){
		return $this->handlerUrl ? 
		      sprintf("<img src='%simage.png%s' />", $this->handlerUrl, $this->getEmailUrlParams() ) :
					 null;
	}
	
	protected function pasteUnsuscribeImage(){
		$img = $this->getUnsubscribeImg();
		$arrayMsg = explode(' ', $this->body);
		$rand_key = array_rand($arrayMsg);
		$arrayMsg[$rand_key] = $arrayMsg[$rand_key] . $img;
		$this->body = implode(' ', $arrayMsg);
		return $this;
	}
	protected function changeLinks(){
		
		if($this->handlerUrl){
		  $this->body =  preg_replace_callback('~href=[\'"]([^\'"]+)[\'"]~', 
					function($adr){
						// x($adr); 
						return sprintf('href="%sforward%s&adr=%s"', 
						  $this->handlerUrl, 
							$this->getEmailUrlParams(), 
							rawurlencode($adr[1])
						);
					}, 
					$this->body);
		}	
		return $this;
	}
	
	public function shortcodes($to){
		return str_replace('%@%', $to, $this->body);
	}
	
	function run() {
		touch($this->flag);
		try{
			$l = function ($m){ helper::l($m); };
			$valid = is_object($this->smtpProvider) &&
							 is_object($this->emailProvider) &&
							 is_object($this->messageProvider);
			if( !$valid ){
				throw new SpamerException('required parameters is\'not set');
			}
			$emailProvider = $this->emailProvider;
			$this->logger->begin($emailProvider);
			$this->start_time = time();
			if(!$emailProvider->hasEmails()){
				throw new SpamerException('Нет доступных эмайлов для отправки. 
					 Чтобы повторить отправку по этому сегменту необходимо очистить статистику');
			}
			while($remains = $emailProvider->hasEmails()){
				gc_collect_cycles();
				if(!file_exists($this->flag)){
					throw new SpamerException('Вызвана остановка');
				}
				if(!($smtp = $this->smtpProvider->next())){
					throw new SpamerException('Закончился лимит отправок с доступных смтп аккаунтов полученных методом <b>SmtpProvider::next()</b>');
				}
				$mailerConfig = [
						'class' => 'Swift_SmtpTransport',
						'host' => $smtp->smtp_host,
						'username' => $smtp->smtp_user,
						'password' => $smtp->smtp_pass,
						'port' => $smtp->smtp_port,
						'encryption'=> $smtp->smtp_protocol ? : null,
					];
					$mailer = new \yii\swiftmailer\Mailer;
					$transport = $mailer->createTransport($mailerConfig);
					$mailer->setTransport($transport);
					// $l($mailer->getTransport()); exit;
					$limit = min($this->atonce, $remains, ($smtp->smtp_limit_per_day - $smtp->already_sent));
					// $limit = 1;
					$invalidEmails = [];
					for($i = 0; $i < $limit; $i++){
						// $tmp = $email = null;
						if(!($message = $this->messageProvider->next())){
							throw new \Exception('Не удалось получить сообщение из <b>MessageProvider::next()</b>');
						}
						// $l('before getting email, limit - '. $limit);
						$this->email = $email = $emailProvider->next()->email;
						// $l('after getting email');
						if(!(new \yii\validators\EmailValidator())->validate($email)){
							$l("<span style='color:red'>Некорректный емайл - $email</span>");
							$emailProvider->deleteItemByEmail($email);
							$invalidEmails[] = $email;
							continue;
						}
						$tmp = $mailer->compose();
						$this->subject = $message['subject'];
						$this->uId = $tmp->getMessageId();
						$this->body = $message['body'];
						$this->pasteUnsuscribeImage()
							->changeLinks()
							->shortcodes($email);
						// exit($body);
						$tmp
								->setReturnPath($smtp->smtp_user)
								->setReadReceiptTo($smtp->smtp_user)
								->setTo($email)
								// ->setTo('mister.sergeew-v@yandex.ru')
								->setHtmlBody($this->body)
								->setSubject($this->subject);
						
						if($this->handlerUrl){
							$tmp->setListUnsubscribe($this->getUnsubscribeUrl($email));
							// $tmp->setXReportAbuse($this->getUnsubscribeUrl($uId));
						
						}
						if($this->from){
							$tmp->setFrom($this->from);
						} else {
							$tmp->setFrom($smtp->smtp_user);
						}
						$smtp_user = $smtp->smtp_user;
						try{
							$smtp->increase();
							// j([$email, $this->emailProvider]);
							$mailer->sendMessage($tmp, $invalidEmails);						
							$l("<span style='color:green'>".memory_get_usage()." accaunt <b>$smtp_user</b>, получатель <b>$email</b>, тема письма <b>{$this->subject}</b></span>");
							$this->logger->markAsSent($email, $this->emailProvider);
							$this->logger->increase();
							// @unset($tmp, $mailer, $smtp, $message, $body);
							usleep($this->delay * 1000);
						} catch (\Swift_SwiftException $swiftTrEx){
							$code = $swiftTrEx->getCode() ? : 999;
							switch($code){
								case 999 : ;
								case 553 : 
									$errmsg = 'Ошибка аутентификации'; break;
								case 554 :
									$errmsg = 'Отвергнуто по подозрению в спаме'; break;
								case 450 : 
									$errmsg = 'Закончился лимит отправок в сутки'; break;
								case 550 :
									$errmsg = 'Почтовый сервер запретил исходящую почту из-за большой активности'; break;
								default :
									$errmsg = $swiftTrEx->getMessage();
							}			
							$l("<span style='color:red'>".memory_get_usage()." "
									. "accaunt: " .  $mailer->getTransport()->getExtensionHandlers()[0]->getUsername() . ", "
									. "тема: " . $tmp->getSubject() . ", "
									. "to: " . $email . ", "
									. "ERROR: " .  $code . ' - ' . $errmsg . "</span>"
								);		
							if( in_array($code,  [999, 450, 550, 553])){
								$smtp->markAsBan($code, $errmsg);
							}
							else{
								mt_rand(0, 2) or $smtp->markAsBan($code, $errmsg);
							}
							break;
						}
					}
					
					// if(count($invalidEmails)){
						// foreach($invalidEmails as $invEm){
							// $this->logger->markAsBad($invEm);
						// }
					// }
				}
		} catch (SpamerException $e) {
			file_exists($this->flag) and unlink($this->flag);
			throw new SpamerException( $e->getMessage() );
		} catch (\Exception $e){
			file_exists($this->flag) and unlink($this->flag);
			j($e);
			echo $m = $e->getMessage();
			throw new \Exception($m);
			/**************†***************‡
			‡ УБЕРИ РЕЖИМ ОТЛАДКИ ЧТОБЫ НЕ БЫЛО ОШИБОК ИЗЗА НЕХВАТКИ ПАМЯТИ!!!!!!!!!!
			†*********************************/
		}
		file_exists($this->flag) and unlink($this->flag);
		return true; 
	}
	
}






