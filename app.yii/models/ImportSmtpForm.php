<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
/**
 * ContactForm is the model behind the contact form.
 */
class ImportSmtpForm extends Model
{
    public $smtpFile;
   
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
    		['smtpFile', 'required'], 
            [['smtpFile'], 'file', 'extensions'=>'txt'], 
        ];
    }
		
	public function export(){
		$smtps = \app\models\Smtp::find()->all();
		$smtp_line = '';
		foreach($smtps as $smtp){
			// $smtp_line .= implode(' ', $smtp->attributes()) . "\r\n";
			$smtp_line .= $smtp->smtp_user . ' ' . $smtp->smtp_pass . ' ' . $smtp->smtp_port . ' ' . $smtp->smtp_protocol . ' ' . $smtp->smtp_host . ' ' . $smtp->smtp_limit_per_day . "\r\n";
		}
		return $smtp_line; 
	}
		
		public function import() {
			$smtps = file ( $this->smtpFile->tempName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
			if(!count($smtps)){
				return false;
			}
			
			$smtps = array_unique ( array_map ( 'trim', array_map ('strtolower',  $smtps ) ) );
			
			foreach($smtps as $smtp_row){
				$smtp_ar = explode(' ',trim($smtp_row));
				$smtp = [
					'smtp_user'=>$smtp_ar[0],
					'smtp_pass'=>$smtp_ar[1],
					'smtp_port'=>(int)$smtp_ar[2],
					'smtp_protocol'=>$smtp_ar[3],
					'smtp_host'=>$smtp_ar[4],
					'smtp_limit'=>isset($smtp_ar[5])? $smtp_ar[5] : 100,
					'already_sent'=>0,
					'is_banned'=>0,
					'ban_reason'=>'',
					];
				$item = \app\models\Smtp::find()->where(['smtp_user'=>$smtp['smtp_user']])->exists();
				if(!$item){
					$item = new \app\models\Smtp;
					$item->attributes = $smtp;
					$item->save();
				}
			}
			return true;
		}
		
		public function load($data,$formName = null){
	    if(!($parent = parent::load($data, $formName))){
				return $parent;
			}
			$this->smtpFile = UploadedFile::getInstance($this, 'smtpFile');
			return $parent;
		}
		
    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
			'smtpFile' => 'Выбрать файл с серверами смтп для загрузки',
        ];
    }
	
    public function attributeHints()
    {
        return [
			'smtpFile' => 'smtp_user smtp_password smtp_port smtp_ptotocol smtp_host smtp_limit',
        ];
    }

}








