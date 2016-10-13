<?php

namespace app\models;

use Yii;
use app\models\Passing;
use app\models\Segment;

/**
 * This is the model class for table "email".
 *
 * @property string $email_id
 * @property string $email
 * @property string $passing_id
 * @property string $site
 * @property string $is_exists
 */
class Email extends \yii\db\ActiveRecord
{
	  # дополнителтные виртуальные свойства для выборки статистмки :
		# название сегмента, кол-во емайлов , ид сегмента
	  public $segment;
		public $c;
		public $sid;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'email';
    }

	public static function primaryKey($asArray = false){
		return ['email'];
	}
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'passing_id','segment_id'], 'required'],
            [['passing_id','segment_id','sended','unsubscribed'], 'integer'],
            [['email'], 'email']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Email',
            'passing_id' => 'Passing ID',
        ];
    }
		
		public function hasEmails(){
			
			return $this->getEmailsBySegmentId()
			      ->select('count(*) c')
						->andWhere('sended = 0')
						->andWhere('unsubscribed = 0')
						->count()
						;
			
		}
		
		public static function deleteItemByEmail($email){
			$e = static::findOne(['email'=>$email]);
			if($e){
				$e->delete();
				return true;
			}
		  return false;
		}
		public function setSegmentId($sid){
			$this->segmentId = $sid;
		}
	
		public function getSegmentId(){
			return $this->segmentId;
		}
		
		public function next($quantity = 1){
		  $sub = $this->getEmailsBySegmentId()
						->select('email')
						->andWhere('sended = 0')
						->andWhere('unsubscribed = 0')
						// ->orderBy('Rand()')
						->limit($quantity);
			$query = (new \yii\db\Query)
				->from(['em'=>$sub])
				->select('em.email')
				->orderBy('rand()')
				->all();
				return $query;
			
		}
		
		public static function createProviderBySegmentId($sid){
			$email = new Email;
			$email->segmentId = $sid;
			return $email;
		}
		
		public function getEmailsBySegmentId(){
			$selfTable = self::tableName();
			// $segmentTable = Segment::tableName();
			$aq = self::find()
			       // ->innerJoin($segmentTable, "$selfTable.segment_id = $segmentTable.segment_id")
						 ->andWhere(["$selfTable.segment_id"=>$this->segmentId])
						 ;
			return $aq;
		}
		
		
}
