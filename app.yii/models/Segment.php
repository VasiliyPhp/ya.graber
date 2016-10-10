<?php

namespace app\models;

use yii\db\Query;
use Yii;
use app\models\Email;

/**
 * This is the model class for table "segment".
 *
 * @property string $segment_id
 * @property string $segment
 */
class Segment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'segment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['segment'], 'required'],
            [['segment'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'segment_id' => 'Segment ID',
            'segment' => 'Segment',
        ];
    }
	
	public static function stat(){
		$query = Email::find()
			->select('segment, count(email) c, s.segment_id sid')
		  ->groupBy('s.segment_id')
			->orderBy('c desc')
			->rightJoin('segment s', 's.segment_id =email.segment_id');
		return $query;
	}
		
}








