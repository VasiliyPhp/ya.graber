<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
/**
 * ContactForm is the model behind the contact form.
 */
class ImportForm extends Model
{
    public $segment_id;
    public $emailsFile;
    public $segments;
   
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
			[['segment_id', 'emailsFile'], 'required'], 
            [['emailsFile'], 'file', 'extensions'=>'txt'], 
        ];
    }
		
		public function import(){
			$emails = file($this->emailsFile->tempName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			if(!count($emails)){
				return false;
			}
			
			$emails = array_unique ( array_map ( 'trim', array_map ('strtolower',  $emails ) ) );
			$passing = new \app\models\Passing;
			$segment_id = $passing->segment_id = $this->segment_id;
			$passing->date = time();
			$passing->query = '% import from file %';
			
			$passing->save();
		  $passing_id = $passing->passing_id;
			
			unset($passing);
			foreach($emails as $email){
				$item = \app\models\Email::find()->where(['email'=>$email])->exists();
				if(!$item){
				$item = new \app\models\Email;
					$item->attributes = compact('email','passing_id', 'segment_id');
					if($item->validate()){
						$item->save();
					}
				}
			}
			return true;
		}
		
		public function load($data,$formName = null){
	    if(!($parent = parent::load($data, $formName))){
				return $parent;
			}
			$this->emailsFile = UploadedFile::getInstance($this, 'emailsFile');
			return $parent;
		}
		
    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'segment_id' => 'Сегменты',
						'emailsFile' => 'Выбрать файл с емайлами для загрузки',
        ];
    }

}








