<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "smtp".
 *
 * @property integer $smtp_id
 * @property string $smtp_user
 * @property string $smtp_pass
 * @property integer $smtp_port
 * @property string $smtp_host
 * @property string $smtp_protocol
 * @property integer $smtp_limit_per_day
 * @property integer $first_run_date
 * @property integer $already_sent
 */
class Smtp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'smtp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['smtp_user', 'smtp_pass', 'already_sent', 'smtp_host'], 'required'],
            [['smtp_user', 'smtp_protocol', 'smtp_host'], 'string'],
            [['smtp_port', 'smtp_limit_per_day', 'first_run_date', 'last_run_date', 'already_sent', 'is_banned'], 'integer'],
            [['smtp_pass'], 'string', 'max' => 100],
            [['ban_reason'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'smtp_id' => 'ид смтп',
            'smtp_user' => 'юзернейм@хост.ру',
            'smtp_pass' => 'Пароль юзера',
            'smtp_port' => 'Порт',
            'smtp_protocol' => 'Протокол',
            'smtp_host' => 'Хост',
            'smtp_limit_per_day' => 'Лимит в сутки',
            'first_run_date' => 'Время первого запуска после сброса лимита',
            'last_run_date' => 'Время последнего использования',
            'already_sent' => 'Отправлено за сутки',
            'is_banned' => 'Забанен',
            'ban_reason' => 'Причина бана',
        ];
    }

    /**
     * @inheritdoc
     * @return SmtpQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SmtpQuery(get_called_class());
    }
		
		public function increase($quantity = 1){
			if(!$this->first_run_date){
				$this->first_run_date = time();
			}
			$this->already_sent += $quantity;
			$this->last_run_date = time();
			$this->save();
		}
		
		public function markAsBan($code = 0, $reason = ''){
			$this->is_banned = $code;
			$this->ban_reason = $reason;
			$this->save();
		}
		
		public static function bann(){
			self::updateAll(['is_banned'=>1, 'ban_reason'=>''], ['is_banned'=>0]); 
		}
		
		public static function unbann(){
			self::updateAll(['is_banned'=>0, 'ban_reason'=>''], ['<>', 'is_banned', 0]); 
		}
		
		public static function unlimit(){
			self::updateAll(['already_sent'=>0]); 
		}
		
		public static function next () {
			
			self::checkState();
			
			$smtp = self::find()
							->where(['is_banned'=>0])
							->orWhere(['is_banned'=>null])
							->andWhere(['or', 
							  'already_sent < smtp_limit_per_day', 
								['or', 
								  'first_run_date is null', 
									'from_unixtime(first_run_date, "%Y%m%d") < '.date('Ymd')
								]
							])
							->orderBy('last_run_date')
							->one()
							;
			// exit(print_r($smtp));
			if($smtp){
				return $smtp;
			}
			return  false;
	  }
		
		private static function checkState(){
			
			// self::updateAll(['first_run_date' =>  0, 'already_sent' => 0, 'is_banned' => 0, 'ban_reason'=>''], 
			self::updateAll(['first_run_date' =>  0, 'already_sent' => 0], 
			'from_unixtime(first_run_date, "%Y%m%d") < '.date('Ymd') .' or first_run_date is null' );
					
		}
		
}










