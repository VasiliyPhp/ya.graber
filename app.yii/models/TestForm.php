<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
/**
 * ContactForm is the model behind the contact form.
 */
class TestForm extends Model
{
    public $smtp;
		public $to;
		public $from;
		public $theme;
		public $body;
   
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
			// print_r($this->fields());exit;
        return [
		      [['from', 'to'], 'safe' /* 'email', 'message'=>'Емайл' */],
					[['smtp','to','theme','body'], 'required','message'=>'обязательно']
        ];
    }
		
    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
         ];
    }

}
