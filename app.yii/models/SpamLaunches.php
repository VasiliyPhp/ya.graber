<?php

namespace app\models;

use Yii;
use yii\db\Query;
/**
 * This is the model class for table "spam_launches".
 *
 * @property integer $launch_id
 * @property string $launch_date
 *
 * @property LaunchItem[] $launchItems
 */
class SpamLaunches extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'spam_launches';
    }
		
		/* related data with table launch_item*/
		public $countRead;
		public $countBounced;
		public $segment;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['launch_date', 'segment'], 'safe'],
            [['total_sending', 'bad_sending', 'segment_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'launch_id' => 'Launch ID',
            'segment_id' => 'Segment ID',
            'segment' => 'Сегмент',
            'launch_date' => 'Время запуска рассылки',
            'total_sending' => 'Отправлено писем за рассылку',
            'bad_sending' => 'Количество неотправленных писем за рассылку',
        ];
    }

		public function begin($emailProvider){
			$this->segment_id = $emailProvider->segmentId;
			$this->save();
		}
		
		public function markAsBad($email){
			$this->increaseBadSending(1);
			\app\models\Email::updateAll(['sended'=>1],"sended=0 and segment_id=$seg_id and email='$email'");
		}
		
		public function increase($quantity = 1){
			$this->updateCounters(['total_sending'=>$quantity]);
		}
		
		public function increaseBadSending($quantity){
			$this->updateCounters(['bad_sending'=>$quantity]);
		}
		
		public function markAsSent($email){
		$seg_id = $this->segment_id;
		\app\models\Email::updateAll(['sended'=>1],"sended=0 and segment_id=$seg_id and email='$email'");

      // $email = \app\models\Email::findOne(['email'=>$email]);
			// if(!$email){
				// return true;
			// }
			// $email->is_exists = 1;
			// $email->save();
		
			// $item = new \app\models\LaunchItem;
			// $item->email_id = $email->email_id;
			// $item->launch_id = $this->launch_id;
			// $item->message_id = $mId;
			// $item->save();
			// echo "<pre>",print_r([$item,$email],1),"</pre>";
		}
		
		public static function stat($by, $date) {
			$groupBy = $by;
			switch($date){
			case 'yestarday':
			  $mysql_where_date = 'from_unixtime(action_time, "%Y%m%d") =' . date('Ymd', strtotime('-1 day'));
				break;
			case 'today':
			  $mysql_where_date = 'from_unixtime(action_time, "%Y%m%d") =' . date('Ymd');
				break;
			case 'month':
			  $mysql_where_date = 'from_unixtime(action_time, "%Y%m") =' . date('Ym');
				break;
			}
			$aq = \app\models\LaunchItem::find()
  			->select([
				  $by,
					'count(if(action_type=0,1,NULL)) as c_opened',
					'count(if(action_type=1,1,NULL)) as c_clicked',
					'count(if(action_type=2,1,NULL)) as c_unsubscribed',
					])
				->andWhere($mysql_where_date)
				->groupBy($groupBy)
				->orderBy('launch_date desc')
				;
				// j($aq);
				// exit;
			return $aq;
		}
		
		public static function sendedStat(){
			 
			$aq = static::find()
			  ->select([
				  'launch_date',
					'total_sending',
					'bad_sending',
					'segment',
				])
				->innerJoin('segment','segment.segment_id = spam_launches.segment_id')
				->orderBy('launch_date desc');
				return $aq;
		}
		
}

