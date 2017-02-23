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
            [['enable_yandex_message_source','interval_between_runs','atempt_count_before_stop', 'send_at_once'], 'integer'],
			[['address_unsubscribe_processing'],'url'],
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
            'atempt_count_before_stop' => 'Количество попыток отправки перед автоматическим отключением',
            'enable_yandex_message_source' => 'Подключить в качестве источника "yandex search results" (не лезть сюда!!!!)',
            'address_unsubscribe_processing' => 'Url адрес для обработки отписок (не лезть сюда!!!!)',
        ];
    }
		
		public static function getYandexEnable(){
			return static::find('id=1')->select('enable_yandex_message_source')->scalar();
		}
}
