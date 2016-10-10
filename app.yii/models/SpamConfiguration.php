<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "spam_configuration".
 *
 * @property integer $id
 * @property integer $interval_between_runs
 * @property integer $send_at_once
 */
class SpamConfiguration extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'spam_configuration';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['interval_between_runs', 'send_at_once'], 'required'],
            [['interval_between_runs', 'send_at_once'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'interval_between_runs' => 'Интервал между отправками в МИЛИСЕКУНДАХ',
            'send_at_once' => 'Количество отправляемых писем с одного аккаунта при одном подключении',
        ];
    }
}
