<?php
	namespace app\common;
	use app\common\helper;
	
	class Spamer extends \yii\base\Object {
		const COOL_TIME = 30;
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
		public $unsubscribe_process_address;
		public $subId;
		public $flag;
		public $atemptCountBeforeStop = 20;
		private $remainsAtempt;
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
		
		private function shortCodes(){
			$urlParams = $this->getEmailUrlParams();
			$email = $this->email;
			$body = $this->body;
			$unsubscribe_process_address = $this->unsubscribe_process_address ? $this->unsubscribe_process_address . $urlParams : null;
			$this->body = str_replace('%a%', $email, $this->body);
			$this->body = str_replace('%img%', $this->getUnsubscribeImg(), $this->body);
			$this->body = preg_replace_callback('~%unsubscribe(?:-([^-%]+))?(?:-([^-%]+))?%~', function($item) use ($unsubscribe_process_address) {
				if(!$unsubscribe_process_address){
					return null;
				}
				// echo '<pre>';print_r($item);die;
				$text = isset($item[1]) ? $item[1] : 'Отписаться';
				$style = isset($item[2]) ? $item[2] : null;
				$link = '<a href="'.$unsubscribe_process_address  . '" ' . ($style ? 'style="'.$style.'" ' : '') . '>'.$text.'</a>';
				return $link;
			}, $this->body);
			// echo $this->body;
			// die;
			return $this;
		}
		
		public function getUnsubscribeImg(){
			return $this->handlerUrl ? 
			sprintf("<img src='%simage.png%s' />", $this->handlerUrl, $this->getEmailUrlParams() ) :
			null;
		}
		
		private function resetAtemptCount(){
			$this->remainsAtempt = $this->atemptCountBeforeStop;
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
		
		function run() {
			touch($this->flag);
			$this->resetAtemptCount();
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
					$transport->setLocalDomain($smtp->smtp_host);
					$mailer->setTransport($transport);
					$limit = min($this->atonce, $remains, ($smtp->smtp_limit_per_day - $smtp->already_sent));
					$email_array = $emailProvider->next($limit);
					$limit = min($limit, count($email_array));
					// $l($limit);
					$invalidEmails = [];
					for($i = 0; $i < $limit; $i++){
						$email = $this->email = $email_array[$i]['email'];
						if(!($message = $this->messageProvider->next())){
							throw new \Exception('Не удалось получить сообщение из <b>MessageProvider::next()</b>');
						}
						if(!mt_rand(0, 30)){
							$l(sprintf('Остываем %s секунд', self::COOL_TIME));
							sleep(self::COOL_TIME);
						}
						if(!(new \yii\validators\EmailValidator())->validate($email)){
							$l("<span style='color:red'>Некорректный емайл - $email</span>");
							$emailProvider->deleteItemByEmail($email);
							$invalidEmails[] = $email;
							continue;
						}
						$tmp = $mailer->compose();
						$privKeyFile = \yii::getAlias('@app') . '/models/smtp.dkim/' . $smtp->smtp_host . '.pem';
						$selectorFile = \yii::getAlias('@app') . '/models/smtp.dkim/' . $smtp->smtp_host . '.key';
						if(file_exists($privKeyFile)){
							$privateKey = file_get_contents($privKeyFile);
							$selector = file_get_contents($selectorFile);
							$signer = new \Swift_Signers_DKIMSigner($privateKey, $smtp->smtp_host, $selector);
							$signer->ignoreHeader('Return-Path');
							$tmp->getSwiftMessage()->attachSigner($signer);
						}
						$this->subject = $message['subject'];
						$this->uId = $tmp->getMessageId();
						$this->body = $message['body'];
						// $this->pasteUnsuscribeImage()
						// ->changeLinks();
						
						$this->shortCodes();
						// echo $this->body;
						// die;
						
						$tmp->setReturnPath($smtp->smtp_user)
						->setReadReceiptTo($smtp->smtp_user)
						->setTo($email)
						->setHtmlBody($this->body)
						->setSubject($this->subject);
						
						if($this->handlerUrl){
							$tmp->setListUnsubscribe($this->getUnsubscribeUrl());
							// $tmp->setXReportAbuse($this->getUnsubscribeUrl());
							
						}
						if($this->from && !preg_match('~@mail\.ru$~', $smtp->smtp_user)){
							$tmp->setFrom($this->from);
							} else {
							$tmp->setFrom($smtp->smtp_user);
						}
						$smtp_user = $smtp->smtp_user;
						try{
							$smtp->increase();
							// j([$email, $this->emailProvider]);
							$mailer->sendMessage($tmp, $invalidEmails);						
							$this->logger->markAsSent($email);
							$l("ящиков:<b> $remains</b>, <span style='color:green'>".memory_get_usage()." from: <b>$smtp_user</b>, to: <b>$email</b>, theme: <b>{$this->subject}</b></span>");
							$this->resetAtemptCount();
							$this->logger->increase();
							// @unset($tmp, $mailer, $smtp, $message, $body);
							usleep($this->delay * 1000);
							} catch (\Swift_SwiftException $swiftTrEx){
							$this->remainsAtempt -= 1;
							if(!$this->remainsAtempt){
								throw new SpamerException("Обсолютный бан после " . $this->atemptCountBeforeStop . " попыток");
							}
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
								$emailProvider->deleteItemByEmail($email);
								$errmsg = sprintf('Hard-bounce - %s', 'ящик недоступен'); break;
								default :
								$this->logger->markAsSent($email);
								$errmsg = $swiftTrEx->getMessage();
							}
							$l($this->remainsAtempt . " попыток осталось. <span style='color:red'>".memory_get_usage()." "
							. "from: " .  $mailer->getTransport()->getExtensionHandlers()[0]->getUsername() . ", "
							. "theme: " . $tmp->getSubject() . ", "
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
				// j($e);
				$m = $e->getMessage() . '<br/>';
				// trigger_error($m);
				throw new \yii\base\ErrorException($e);
				return false;
				/**************†***************‡
					‡ УБЕРИ РЕЖИМ ОТЛАДКИ ЧТОБЫ НЕ БЫЛО ОШИБОК ИЗ-ЗА НЕХВАТКИ ПАМЯТИ!!!!!!!!!!
				†*********************************/
			}
			file_exists($this->flag) and unlink($this->flag);
			return true; 
		}
		
	}
	
	
	
	
	
	
