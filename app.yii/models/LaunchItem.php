<?php

namespace app\models;

use Yii;
use app\models\Email;
/**
 * This is the model class for table "launch_item".
 *
 * @property integer $launch_id
 * @property string $mesage_id
 * @property string $email_id
 * @property integer $is_read
 * @property integer $is_bounced
 * @property integer $reading_date
 *
 * @property SpamLaunches $launch
 * @property Messages $mesage
 * @property Email $email
 */
class LaunchItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
		const OPENED = 0;
		const CLICKED = 1;
		const UNSUBSCRIBED = 2;
		
		public $c_opened = 0;
		public $c_clicked = 0;
		public $c_unsubscribed = 0;
		public $cc;
		
    public static function tableName()
    {
        return 'launch_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['launch_date', 'action_type', 'uid'], 'required'],
            [['subid', 'uid', 'theme', 'clicked_link'], 'string'],
            [['action_type', 'action_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'subid' => 'SubId',
						'c_clicked'=>'Перешли по ссылкам из письма',
						'c_opened'=>'Откоыто писем',
						'c_unsubscribed'=>'Отписалось',
        ];
    }
		
		public function filterColumns(){
			return [
			  'theme'=>'По теме письма',
				'subid'=>'По Subid',
			];
		}
		
		// public static function primaryKey(){
			
			// return ['message_id'];
			
		// }
		
		public static function unsubscribe($email = null, $date = null, $uid = null, $theme = null, $subid = null){
			$launchItem = self::find()->where(["uid"=>$uid, 'action_type'=>self::UNSUBSCRIBED])->one();
			if($launchItem){
				return false;
			} else {
				$launchItem = new self;
			}
			$launchItem->action_time = time();
			$launchItem->action_type = self::UNSUBSCRIBED;
			$launchItem->subid = $subid;
			$launchItem->theme = $theme;
			$launchItem->uid = $uid;
			$launchItem->launch_date = $date;
			$launchItem->save();
			
			Email::updateAll(['unsubscribed'=>1], ['email'=>$email]);
		
		}
		public static function open($email = null, $date = null, $uid = null, $theme = null, $subid = null){
		  $launchItem = self::find()->where(["uid"=>$uid, 'action_type'=>self::OPENED])->one();
			if($launchItem){
				return false;
			} else {
				$launchItem = new self;
			}		
      // echo ('5');			
			$launchItem->action_time = time();
			$launchItem->action_type = self::OPENED;
			$launchItem->subid = $subid;
			$launchItem->theme = $theme;
			$launchItem->uid = $uid;
			$launchItem->launch_date = $date;
			$launchItem->save();
			// j($launchItem);
			
		}
		public static function click($email = null, $date = null, $uid = null, $theme = null, $subid = null, $adr){
			$launchItem = self::find()->where(["uid"=>$uid, 'action_type'=>self::CLICKED, 'clicked_link'=>$adr])->one();
			if($launchItem){
				return false;
			} else {
				$launchItem = new self;
			}			
			$launchItem->clicked_link = $adr;
			$launchItem->action_time = time();
			$launchItem->action_type = self::CLICKED;
			$launchItem->subid = $subid;
			$launchItem->theme = $theme;
			$launchItem->uid = $uid;
			$launchItem->launch_date = $date;
			$launchItem->save();
			
		}
}
