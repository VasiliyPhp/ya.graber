<?php
	
	namespace app\models;
	
	use Yii;
	use yii\base\Model;
	use yii\web\UploadedFile;
	/**
		* ContactForm is the model behind the contact form.
	*/
	class SpamForm extends Model
	{
		public $messageSource;
		public $messageTemplate;
		public $messageSubject;
		public $segmentSource;
		public $prefixFile;
		public $rootFile;
		public $messageFrom;
		public $handleUrl;
		public $setListUnsuscribe;
		public $subId;
		
		/**
			* @return array the validation rules.
		*/
		public function rules()
		{
			return [
            ['messageFrom', 'email', 'message'=>'Емайл'],
            ['messageSubject', 'required', 
			'when'=>function(){
				return $this->messageSource == 'html';
			},
			'message'=>'Обязательно',
			'whenClient'=>'function(attr, val){return $("#spamform-messagesource").val()=="html";}',
			],
            [['prefixFile', 'rootFile'], 'file', 'extensions'=>'txt'], 
            [['prefixFile'], 'required', 
			'when'=>function(){
				return $this->messageSource == 'yandex';
			},
			'message'=>'Обязательно',
			'whenClient'=>'function(attr, val){return $("#spamform-messagesource").val()=="yandex";}',
			],
            [['segmentSource', 'messageTemplate', 'messageSource', 'subId'],  'required', 'message'=>'Обязательно'],
			['handleUrl', 'url', 'message'=>'Должен быть url адрес'],
			['setListUnsuscribe', 'boolean'],
			];
		}
		
		public function load($data,$formName = null){
			// \l($data);
			if(!($parent = parent::load($data, $formName))){
				return $parent;
			}
			if($this->messageSource == 'yandex'){
				$this->prefixFile = UploadedFile::getInstance($this, 'prefixFile');
				$this->rootFile = UploadedFile::getInstance($this, 'rootFile');
			}
			return $parent;
		}
		
		/**
			* @return array customized attribute labels
		*/
		public function attributeLabels()
		{
			return [
			'messageSource' => 'Источник сообщений',
			'messageSubject' => 'Тема сообщения',
			'messageFrom' => 'From:',
			'messageTemplate' => 'Шаблон сообщения',
			'segmentSource' => 'Сегмент емайл адресов',
			'prefixFile' => 'Основной поисковый запрос (список тем писем)',
			'rootFile' => 'Дополнительный текст, прибавляемый к каждому поисковому запросу',
			'handleUrl' => 'Адрес сервера обработки',
			'subId' => 'Уникальный идентификатор рассылки для дальнейшего отслеживания',
			'setListUnsuscribe' => 'Устанавливать заголовок отписки',
			];
		}
		
		public function attributeHints()
		{
			return ['setListUnsuscribe'=>'Устанавливает кнопку "отписаться" в стандартном интерфейсе почтовика (но не всегда работает)'];
		}
		
	}
