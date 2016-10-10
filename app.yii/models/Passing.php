<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "passing".
 *
 * @property string $passing_id
 * @property string $segment_id
 * @property string $query
 * @property integer $date
 * @property integer $count_page
 */
class Passing extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'passing';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['segment_id', 'date'], 'required'],
            [['segment_id', 'date'], 'integer'],
            [['query'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'passing_id' => 'Passing ID',
            'segment_id' => 'Segment ID',
            'query' => 'Query',
            'date' => 'Date',
            'count_page' => 'Count Page',
        ];
    }
}
